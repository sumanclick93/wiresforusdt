<?php

namespace App\Helpers;

class PdfGenerator
{
    private $buffer = '';
    private $offsets = [];
    private $objects = [];
    private $pages = [];
    private $fontId = 0;
    
    public function __construct()
    {
        $this->buffer = "%PDF-1.4\r\n";
    }

    private function newObject($data)
    {
        $id = count($this->objects) + 3; // Reserve 1 for Catalog, 2 for Pages catalog
        $this->objects[$id] = $data;
        return $id;
    }

    /**
     * Generate PDF from user and application details.
     */
    public static function generate(object $user, ?object $application): string
    {
        $pdf = new self();
        
        $parseFiles = function(?string $val) {
            if (empty($val)) return [];
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [$val];
        };

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'wiresforusdt.com';

        // Compile lines of text to output
        $lines = [];
        $lines[] = ["TITLE", "Wires4 Client Profile Report"];
        $lines[] = ["SUBTITLE", "Client ID: " . ($user->login_id ?? 'N/A')];
        $lines[] = ["SUBTITLE", "Generated: " . date('Y-m-d H:i:s')];
        $lines[] = ["SEPARATOR", ""];

        $addField = function(string $label, ?string $value) use (&$lines) {
            $val = $value ?? 'N/A';
            $fullText = $label . ": " . $val;
            if (strlen($fullText) > 80) {
                $wrapped = wordwrap($fullText, 80, "\n", true);
                foreach (explode("\n", $wrapped) as $part) {
                    $lines[] = ["FIELD", $part];
                }
            } else {
                $lines[] = ["FIELD", $fullText];
            }
        };

        $addFileField = function(string $label, string $url) use (&$lines) {
            $lines[] = ["FIELD", $label];
            $lines[] = ["FIELD", "  " . $url];
        };

        $lines[] = ["SECTION", "ACCOUNT INFORMATION"];
        $addField("Client ID", $user->login_id);
        $addField("Full Name", $user->name);
        $addField("Email Address", $user->email);
        $addField("Status", $user->status);
        $addField("Role", $user->role);
        $addField("Created At", $user->created_at);
        $addField("Wallet Address", $user->wallet_address ?: ($application->wallet_address ?? 'N/A'));
        $addField("Network Protocol", $user->network_type ?: ($application->network_type ?? 'N/A'));
        if ($application) {
            $addField("Referral Source", $application->referral_source);
            $addField("Referral Code", $application->referral_code);
        }
        $lines[] = ["SEPARATOR", ""];

        if ($application) {
            $isCorporate = (($application->account_type ?? '') === 'corporate');
            
            $lines[] = ["SECTION", "APPLICATION DETAILS"];
            $addField("Account Type", ucfirst($application->account_type ?? 'N/A'));
            
            if (!$isCorporate) {
                // Individual fields
                $addField("Title/Occupation", $application->title_occupation);
                $addField("First Name", $application->first_name);
                $addField("Middle Name", $application->middle_name);
                $addField("Last Name", $application->last_name);
                $addField("Date of Birth", $application->dob);
                $addField("Country", $application->country);
                $addField("Phone Number", $application->phone_number);
                $addField("Street Address", $application->street_address);
                $addField("Unit/Apt", $application->unit_apartment);
                $addField("City", $application->city);
                $addField("State/Province", $application->state_province);
                $addField("Postal/Zip", $application->postal_zip);
                $addField("LinkedIn", $application->linkedin);
                $addField("Instagram", $application->instagram);
                $addField("Twitter", $application->twitter);
            } else {
                // Corporate fields
                $addField("Entity Type", $application->entity_type);
                $addField("Company Name", $application->company_name);
                $addField("Registration Number", $application->company_reg_number);
                $addField("LEI Identifier", $application->lei_identifier);
                $addField("Incorporation Country", $application->incorporation_country);
                $addField("Incorporation Date", $application->incorporation_date);
                $addField("Nature of Business", $application->nature_of_business);
                $addField("Is Regulated", $application->company_regulated);
                $addField("Accredited Investor", $application->accredited_investor);
                $addField("US Financial Entity", $application->financial_entity_us);
                $addField("Swap Dealer", $application->swap_dealer);
                $addField("Corporate Contact Phone", $application->contact_number);
                $addField("Business Website", $application->website);
                $addField("Operating Address Different", $application->operating_address_different);
                $addField("Street Address", $application->street_address_entity);
                $addField("Country", $application->country_entity);
                $addField("City", $application->city_entity);
                $addField("State", $application->state_entity);
                $addField("Postal Code", $application->postal_entity);
                $addField("LinkedIn", $application->linkedin_entity);
                $addField("Instagram", $application->instagram_entity);
                $addField("Twitter", $application->twitter_entity);
            }
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "FINANCIAL & TRADING PROFILE"];
            $addField("Trading Purpose", $application->trading_purpose);
            $addField("Trading Purpose Details", $application->trading_purpose_desc);
            $addField("Flow of Funds", $application->flow_of_funds);
            $addField("First Trade Date", $application->first_trade_date);
            $addField("First Trade Size", ($application->first_trade_currency ?? '') . ' ' . ($application->first_trade_size ?? ''));
            $addField("Est. Monthly Volume", ($application->monthly_volume_currency ?? '') . ' ' . ($application->monthly_volume_size ?? ''));
            $addField("Source of Funding", $isCorporate ? ($application->source_funding_entity ?? 'N/A') : ($application->source_funding ?? 'N/A'));
            $addField("Annual Income", ($application->annual_income_currency ?? '') . ' ' . ($application->annual_income_amount ?? ''));
            $addField("Liquid Assets", ($application->liquid_assets_currency ?? '') . ' ' . ($application->liquid_assets_amount ?? ''));
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "COMPLIANCE & DECLARATIONS"];
            if (!$isCorporate) {
                // Individual Compliance
                $addField("Declared Bankruptcy", ($application->declared_bankruptcy ?? 'No') . ' (' . ($application->declared_bankruptcy_desc ?? '') . ')');
                $addField("PEP Status", ($application->pep_status ?? 'No') . ' (' . ($application->pep_status_desc ?? '') . ')');
                $addField("High Value Transactions", $application->considerable_transactions);
                $addField("US Accreditations", $application->portfolio_excess);
                $addField("Accredited Investor", $application->accredited_investor);
            } else {
                // Corporate Compliance
                $addField("Declared Bankruptcy", ($application->declared_bankruptcy_entity ?? 'No') . ' (' . ($application->declared_bankruptcy_entity_desc ?? '') . ')');
                $addField("PEP Status", ($application->pep_status_entity ?? 'No') . ' (' . ($application->pep_status_entity_desc ?? '') . ')');
                $addField("US Financial Entity", $application->financial_entity_us);
                $addField("Swap Dealer", $application->swap_dealer);
            }
            $addField("Declaration Signed", $application->declaration_signed ? 'Yes' : 'No');
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "BANKING DETAILS"];
            $addField("Beneficiary Bank", $application->bank_name);
            $addField("Bank Address", $application->bank_address);
            $addField("Bank Country", $application->bank_country);
            $addField("Account Holder", $application->bank_account_holder);
            $addField("Account Number", $application->bank_account_number);
            $addField("Routing Code", $application->bank_routing_code);
            $addField("SWIFT/BIC Code", $application->bank_swift);
            $addField("Account Currency", $application->bank_currency);
            $addField("Beneficiary Address", $application->bank_beneficiary_address);
            $addField("Intermediary Bank", $application->bank_intermediary);
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "UPLOADED DOCUMENTS"];
            if (!$isCorporate) {
                // KYC Files
                $kycFiles = $parseFiles($application->kyc_document_file);
                if (empty($kycFiles)) {
                    $lines[] = ["FIELD", "KYC Document File: N/A"];
                } else {
                    foreach ($kycFiles as $idx => $f) {
                        $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                        $addFileField("KYC Document [" . ($idx + 1) . "]:", $fileUrl);
                    }
                }

                // Proof of Funds
                $fundsFiles = $parseFiles($application->proof_funds_file);
                if (empty($fundsFiles)) {
                    $lines[] = ["FIELD", "Proof of Funds File: N/A"];
                } else {
                    foreach ($fundsFiles as $idx => $f) {
                        $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                        $addFileField("Proof of Funds [" . ($idx + 1) . "]:", $fileUrl);
                    }
                }
            } else {
                $corpDocs = [
                    'entity_articles_file' => 'Articles of Incorporation',
                    'entity_shareholders_file' => 'Shareholder Register',
                    'entity_bank_statement_file' => 'Bank Statement',
                    'entity_proof_address_file' => 'Proof of Address',
                    'entity_board_resolution_file' => 'Board Resolution'
                ];
                foreach ($corpDocs as $colName => $docLabel) {
                    $val = $application->$colName;
                    $files = $parseFiles($val);
                    if (empty($files)) {
                        $lines[] = ["FIELD", $docLabel . ": N/A"];
                    } else {
                        foreach ($files as $idx => $f) {
                            $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                            $addFileField($docLabel . " [" . ($idx + 1) . "]:", $fileUrl);
                        }
                    }
                }
            }
        }

        // Layout page content streams
        $pagesText = [];
        $currentPageLines = [];
        $y = 780;
        
        foreach ($lines as $line) {
            $type = $line[0];
            $txt = $line[1];

            // Estimate spacing
            $spacing = 15;
            if ($type === 'TITLE') $spacing = 30;
            elseif ($type === 'SECTION') $spacing = 25;
            elseif ($type === 'SEPARATOR') $spacing = 10;

            if ($y - $spacing < 50) {
                // Page break
                $pagesText[] = $currentPageLines;
                $currentPageLines = [];
                $y = 780;
            }

            $y -= $spacing;
            $currentPageLines[] = [$type, $txt, $y];
        }
        if (!empty($currentPageLines)) {
            $pagesText[] = $currentPageLines;
        }

        // Font Helvetica with WinAnsiEncoding
        $pdf->fontId = $pdf->newObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $boldFontId = $pdf->newObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");

        $pageReferences = [];

        foreach ($pagesText as $pageIdx => $pageData) {
            $stream = "BT\n";
            $prevY = null;
            
            foreach ($pageData as $elem) {
                $type = $elem[0];
                $txt = $elem[1];
                $ty = $elem[2];

                // Escape text for PDF
                $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $txt);
                
                // Determine layout fonts and sizes
                $font = $pdf->fontId;
                $size = 9.5;
                if ($type === 'TITLE') {
                    $font = $boldFontId;
                    $size = 20;
                } elseif ($type === 'SUBTITLE') {
                    $font = $pdf->fontId;
                    $size = 10;
                } elseif ($type === 'SECTION') {
                    $font = $boldFontId;
                    $size = 12;
                }

                // Calculate relative translation
                if ($prevY === null) {
                    $dx = 50;
                    $dy = $ty;
                } else {
                    $dx = 0;
                    $dy = $ty - $prevY;
                }

                if ($type === 'FIELD' && strlen($escaped) > 85) {
                    $parts = str_split($escaped, 80);
                    foreach ($parts as $pIdx => $part) {
                        $pDy = ($pIdx === 0) ? $dy : -11;
                        $pDx = ($pIdx === 0) ? $dx : 0;
                        $stream .= "/F" . $font . " " . $size . " Tf\n";
                        $stream .= $pDx . " " . $pDy . " Td\n";
                        $stream .= "(" . $part . ") Tj\n";
                    }
                    // Since we split into multiple lines, update prevY to the Y of the last split line
                    $prevY = $ty - ((count($parts) - 1) * 11);
                } else {
                    $stream .= "/F" . $font . " " . $size . " Tf\n";
                    $stream .= $dx . " " . $dy . " Td\n";
                    $stream .= "(" . $escaped . ") Tj\n";
                    $prevY = $ty;
                }
            }
            $stream .= "\nET";

            $streamId = $pdf->newObject("<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream");
            $pageReferences[] = $pdf->newObject("<< /Type /Page /Parent 2 0 R /Resources << /Font << /F" . $pdf->fontId . " " . $pdf->fontId . " 0 R /F" . $boldFontId . " " . $boldFontId . " 0 R >> >> /MediaBox [0 0 595 842] /Contents " . $streamId . " 0 R >>");
        }

        // Page catalog and catalog objects
        $kids = implode(" 0 R ", $pageReferences) . " 0 R";
        $pdf->objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $pdf->objects[2] = "<< /Type /Pages /Kids [" . $kids . "] /Count " . count($pageReferences) . " >>";

        // Sort objects by ID key to ensure correct sequence in PDF body
        ksort($pdf->objects);

        // Build buffer and offsets
        foreach ($pdf->objects as $id => $obj) {
            $pdf->offsets[$id] = strlen($pdf->buffer);
            $pdf->buffer .= $id . " 0 obj\r\n" . $obj . "\r\nendobj\r\n";
        }

        $xrefPos = strlen($pdf->buffer);
        $pdf->buffer .= "xref\r\n";
        $pdf->buffer .= "0 " . (count($pdf->objects) + 1) . "\r\n";
        $pdf->buffer .= "0000000000 65535 f\r\n";
        foreach ($pdf->objects as $id => $obj) {
            $pdf->buffer .= sprintf("%010d 00000 n\r\n", $pdf->offsets[$id]);
        }

        $pdf->buffer .= "trailer\r\n";
        $pdf->buffer .= "<< /Size " . (count($pdf->objects) + 1) . " /Root 1 0 R >>\r\n";
        $pdf->buffer .= "startxref\r\n";
        $pdf->buffer .= $xrefPos . "\r\n";
        $pdf->buffer .= "%%EOF\r\n";

        return $pdf->buffer;
    }
}
