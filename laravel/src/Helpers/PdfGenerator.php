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

        $lines[] = ["SECTION", "ACCOUNT INFORMATION"];
        $lines[] = ["FIELD", "Client ID: " . ($user->login_id ?? 'N/A')];
        $lines[] = ["FIELD", "Full Name: " . ($user->name ?? 'N/A')];
        $lines[] = ["FIELD", "Email Address: " . ($user->email ?? 'N/A')];
        $lines[] = ["FIELD", "Status: " . ($user->status ?? 'N/A')];
        $lines[] = ["FIELD", "Role: " . ($user->role ?? 'N/A')];
        $lines[] = ["FIELD", "Created At: " . ($user->created_at ?? 'N/A')];
        $lines[] = ["FIELD", "Wallet Address: " . ($user->wallet_address ?: ($application->wallet_address ?? 'N/A'))];
        $lines[] = ["FIELD", "Network Protocol: " . ($user->network_type ?: ($application->network_type ?? 'N/A'))];
        if ($application) {
            $lines[] = ["FIELD", "Referral Source: " . ($application->referral_source ?? 'N/A')];
            $lines[] = ["FIELD", "Referral Code: " . ($application->referral_code ?? 'N/A')];
        }
        $lines[] = ["SEPARATOR", ""];

        if ($application) {
            $isCorporate = (($application->account_type ?? '') === 'corporate');
            
            $lines[] = ["SECTION", "APPLICATION DETAILS"];
            $lines[] = ["FIELD", "Account Type: " . ucfirst($application->account_type ?? 'N/A')];
            
            if (!$isCorporate) {
                // Individual fields
                $lines[] = ["FIELD", "Title/Occupation: " . ($application->title_occupation ?? 'N/A')];
                $lines[] = ["FIELD", "First Name: " . ($application->first_name ?? 'N/A')];
                $lines[] = ["FIELD", "Middle Name: " . ($application->middle_name ?? 'N/A')];
                $lines[] = ["FIELD", "Last Name: " . ($application->last_name ?? 'N/A')];
                $lines[] = ["FIELD", "Date of Birth: " . ($application->dob ?? 'N/A')];
                $lines[] = ["FIELD", "Country: " . ($application->country ?? 'N/A')];
                $lines[] = ["FIELD", "Phone Number: " . ($application->phone_number ?? 'N/A')];
                $lines[] = ["FIELD", "Street Address: " . ($application->street_address ?? 'N/A')];
                $lines[] = ["FIELD", "Unit/Apt: " . ($application->unit_apartment ?? 'N/A')];
                $lines[] = ["FIELD", "City: " . ($application->city ?? 'N/A')];
                $lines[] = ["FIELD", "State/Province: " . ($application->state_province ?? 'N/A')];
                $lines[] = ["FIELD", "Postal/Zip: " . ($application->postal_zip ?? 'N/A')];
                $lines[] = ["FIELD", "LinkedIn: " . ($application->linkedin ?? 'N/A')];
                $lines[] = ["FIELD", "Instagram: " . ($application->instagram ?? 'N/A')];
                $lines[] = ["FIELD", "Twitter: " . ($application->twitter ?? 'N/A')];
            } else {
                // Corporate fields
                $lines[] = ["FIELD", "Entity Type: " . ($application->entity_type ?? 'N/A')];
                $lines[] = ["FIELD", "Company Name: " . ($application->company_name ?? 'N/A')];
                $lines[] = ["FIELD", "Registration Number: " . ($application->company_reg_number ?? 'N/A')];
                $lines[] = ["FIELD", "LEI Identifier: " . ($application->lei_identifier ?? 'N/A')];
                $lines[] = ["FIELD", "Incorporation Country: " . ($application->incorporation_country ?? 'N/A')];
                $lines[] = ["FIELD", "Incorporation Date: " . ($application->incorporation_date ?? 'N/A')];
                $lines[] = ["FIELD", "Nature of Business: " . ($application->nature_of_business ?? 'N/A')];
                $lines[] = ["FIELD", "Is Regulated: " . ($application->company_regulated ?? 'N/A')];
                $lines[] = ["FIELD", "Accredited Investor: " . ($application->accredited_investor ?? 'N/A')];
                $lines[] = ["FIELD", "US Financial Entity: " . ($application->financial_entity_us ?? 'N/A')];
                $lines[] = ["FIELD", "Swap Dealer: " . ($application->swap_dealer ?? 'N/A')];
                $lines[] = ["FIELD", "Corporate Contact Phone: " . ($application->contact_number ?? 'N/A')];
                $lines[] = ["FIELD", "Business Website: " . ($application->website ?? 'N/A')];
                $lines[] = ["FIELD", "Operating Address Different: " . ($application->operating_address_different ?? 'N/A')];
                $lines[] = ["FIELD", "Street Address: " . ($application->street_address_entity ?? 'N/A')];
                $lines[] = ["FIELD", "Country: " . ($application->country_entity ?? 'N/A')];
                $lines[] = ["FIELD", "City: " . ($application->city_entity ?? 'N/A')];
                $lines[] = ["FIELD", "State: " . ($application->state_entity ?? 'N/A')];
                $lines[] = ["FIELD", "Postal Code: " . ($application->postal_entity ?? 'N/A')];
                $lines[] = ["FIELD", "LinkedIn: " . ($application->linkedin_entity ?? 'N/A')];
                $lines[] = ["FIELD", "Instagram: " . ($application->instagram_entity ?? 'N/A')];
                $lines[] = ["FIELD", "Twitter: " . ($application->twitter_entity ?? 'N/A')];
            }
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "FINANCIAL & TRADING PROFILE"];
            $lines[] = ["FIELD", "Trading Purpose: " . ($application->trading_purpose ?? 'N/A')];
            $lines[] = ["FIELD", "Trading Purpose Details: " . ($application->trading_purpose_desc ?? 'N/A')];
            $lines[] = ["FIELD", "Flow of Funds: " . ($application->flow_of_funds ?? 'N/A')];
            $lines[] = ["FIELD", "First Trade Date: " . ($application->first_trade_date ?? 'N/A')];
            $lines[] = ["FIELD", "First Trade Size: " . ($application->first_trade_currency ?? '') . ' ' . ($application->first_trade_size ?? '')];
            $lines[] = ["FIELD", "Est. Monthly Volume: " . ($application->monthly_volume_currency ?? '') . ' ' . ($application->monthly_volume_size ?? '')];
            $lines[] = ["FIELD", "Source of Funding: " . ($isCorporate ? ($application->source_funding_entity ?? 'N/A') : ($application->source_funding ?? 'N/A'))];
            $lines[] = ["FIELD", "Annual Income: " . ($application->annual_income_currency ?? '') . ' ' . ($application->annual_income_amount ?? '')];
            $lines[] = ["FIELD", "Liquid Assets: " . ($application->liquid_assets_currency ?? '') . ' ' . ($application->liquid_assets_amount ?? '')];
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "COMPLIANCE & DECLARATIONS"];
            if (!$isCorporate) {
                // Individual Compliance
                $lines[] = ["FIELD", "Declared Bankruptcy: " . ($application->declared_bankruptcy ?? 'No') . ' (' . ($application->declared_bankruptcy_desc ?? '') . ')'];
                $lines[] = ["FIELD", "PEP Status: " . ($application->pep_status ?? 'No') . ' (' . ($application->pep_status_desc ?? '') . ')'];
                $lines[] = ["FIELD", "High Value Transactions: " . ($application->considerable_transactions ?? 'No')];
                $lines[] = ["FIELD", "US Accreditations: " . ($application->portfolio_excess ?? 'No')];
                $lines[] = ["FIELD", "Accredited Investor: " . ($application->accredited_investor ?? 'No')];
            } else {
                // Corporate Compliance
                $lines[] = ["FIELD", "Declared Bankruptcy: " . ($application->declared_bankruptcy_entity ?? 'No') . ' (' . ($application->declared_bankruptcy_entity_desc ?? '') . ')'];
                $lines[] = ["FIELD", "PEP Status: " . ($application->pep_status_entity ?? 'No') . ' (' . ($application->pep_status_entity_desc ?? '') . ')'];
                $lines[] = ["FIELD", "US Financial Entity: " . ($application->financial_entity_us ?? 'No')];
                $lines[] = ["FIELD", "Swap Dealer: " . ($application->swap_dealer ?? 'No')];
            }
            $lines[] = ["FIELD", "Declaration Signed: " . ($application->declaration_signed ? 'Yes' : 'No')];
            $lines[] = ["SEPARATOR", ""];

            $lines[] = ["SECTION", "BANKING DETAILS"];
            $lines[] = ["FIELD", "Beneficiary Bank: " . ($application->bank_name ?? 'N/A')];
            $lines[] = ["FIELD", "Bank Address: " . ($application->bank_address ?? 'N/A')];
            $lines[] = ["FIELD", "Bank Country: " . ($application->bank_country ?? 'N/A')];
            $lines[] = ["FIELD", "Account Holder: " . ($application->bank_account_holder ?? 'N/A')];
            $lines[] = ["FIELD", "Account Number: " . ($application->bank_account_number ?? 'N/A')];
            $lines[] = ["FIELD", "Routing Code: " . ($application->bank_routing_code ?? 'N/A')];
            $lines[] = ["FIELD", "SWIFT/BIC Code: " . ($application->bank_swift ?? 'N/A')];
            $lines[] = ["FIELD", "Account Currency: " . ($application->bank_currency ?? 'USD')];
            $lines[] = ["FIELD", "Beneficiary Address: " . ($application->bank_beneficiary_address ?? 'N/A')];
            $lines[] = ["FIELD", "Intermediary Bank: " . ($application->bank_intermediary ?? 'N/A')];
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
                        $lines[] = ["FIELD", "KYC Document [" . ($idx + 1) . "]: " . $fileUrl];
                    }
                }

                // Proof of Funds
                $fundsFiles = $parseFiles($application->proof_funds_file);
                if (empty($fundsFiles)) {
                    $lines[] = ["FIELD", "Proof of Funds File: N/A"];
                } else {
                    foreach ($fundsFiles as $idx => $f) {
                        $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                        $lines[] = ["FIELD", "Proof of Funds [" . ($idx + 1) . "]: " . $fileUrl];
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
                            $lines[] = ["FIELD", $docLabel . " [" . ($idx + 1) . "]: " . $fileUrl];
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
