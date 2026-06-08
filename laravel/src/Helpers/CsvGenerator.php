<?php

namespace App\Helpers;

class CsvGenerator
{
    /**
     * Generate a CSV string containing all user and application details.
     */
    public static function generate(object $user, ?object $application): string
    {
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

        $data = [
            ['SECTION', 'FIELD', 'VALUE'],
            ['Account Info', 'Client ID', $user->login_id ?? 'N/A'],
            ['Account Info', 'Full Name', $user->name ?? 'N/A'],
            ['Account Info', 'Email Address', $user->email ?? 'N/A'],
            ['Account Info', 'Status', $user->status ?? 'N/A'],
            ['Account Info', 'Role', $user->role ?? 'N/A'],
            ['Account Info', 'Created At', $user->created_at ?? 'N/A'],
            ['Account Info', 'Wallet Address', $user->wallet_address ?: ($application->wallet_address ?? 'N/A')],
            ['Account Info', 'Network Protocol', $user->network_type ?: ($application->network_type ?? 'N/A')],
        ];

        if ($application) {
            $data[] = ['Account Info', 'Referral Source', $application->referral_source ?? 'N/A'];
            $data[] = ['Account Info', 'Referral Code', $application->referral_code ?? 'N/A'];

            $isCorporate = (($application->account_type ?? '') === 'corporate');
            $data[] = ['Application Details', 'Account Type', $application->account_type ?? 'N/A'];
            
            if (!$isCorporate) {
                // Individual fields
                $data[] = ['Personal Info', 'Title/Occupation', $application->title_occupation ?? 'N/A'];
                $data[] = ['Personal Info', 'First Name', $application->first_name ?? 'N/A'];
                $data[] = ['Personal Info', 'Middle Name', $application->middle_name ?? 'N/A'];
                $data[] = ['Personal Info', 'Last Name', $application->last_name ?? 'N/A'];
                $data[] = ['Personal Info', 'Date of Birth', $application->dob ?? 'N/A'];
                $data[] = ['Personal Info', 'Country', $application->country ?? 'N/A'];
                $data[] = ['Personal Info', 'Phone Number', $application->phone_number ?? 'N/A'];
                $data[] = ['Personal Info', 'Street Address', $application->street_address ?? 'N/A'];
                $data[] = ['Personal Info', 'Unit/Apt', $application->unit_apartment ?? 'N/A'];
                $data[] = ['Personal Info', 'City', $application->city ?? 'N/A'];
                $data[] = ['Personal Info', 'State/Province', $application->state_province ?? 'N/A'];
                $data[] = ['Personal Info', 'Postal/Zip', $application->postal_zip ?? 'N/A'];
                $data[] = ['Social Profiles', 'LinkedIn', $application->linkedin ?? 'N/A'];
                $data[] = ['Social Profiles', 'Instagram', $application->instagram ?? 'N/A'];
                $data[] = ['Social Profiles', 'Twitter', $application->twitter ?? 'N/A'];
            } else {
                // Corporate fields
                $data[] = ['Entity Info', 'Entity Type', $application->entity_type ?? 'N/A'];
                $data[] = ['Entity Info', 'Company Name', $application->company_name ?? 'N/A'];
                $data[] = ['Entity Info', 'Registration Number', $application->company_reg_number ?? 'N/A'];
                $data[] = ['Entity Info', 'LEI Identifier', $application->lei_identifier ?? 'N/A'];
                $data[] = ['Entity Info', 'Incorporation Country', $application->incorporation_country ?? 'N/A'];
                $data[] = ['Entity Info', 'Incorporation Date', $application->incorporation_date ?? 'N/A'];
                $data[] = ['Entity Info', 'Nature of Business', $application->nature_of_business ?? 'N/A'];
                $data[] = ['Entity Info', 'Is Regulated', $application->company_regulated ?? 'N/A'];
                $data[] = ['Entity Info', 'Accredited Investor', $application->accredited_investor ?? 'N/A'];
                $data[] = ['Entity Info', 'US Financial Entity', $application->financial_entity_us ?? 'N/A'];
                $data[] = ['Entity Info', 'Swap Dealer', $application->swap_dealer ?? 'N/A'];
                $data[] = ['Entity Info', 'Corporate Contact Phone', $application->contact_number ?? 'N/A'];
                $data[] = ['Entity Info', 'Business Website', $application->website ?? 'N/A'];
                $data[] = ['Entity Info', 'Operating Address Different', $application->operating_address_different ?? 'N/A'];
                $data[] = ['Entity Info', 'Street Address', $application->street_address_entity ?? 'N/A'];
                $data[] = ['Entity Info', 'Country', $application->country_entity ?? 'N/A'];
                $data[] = ['Entity Info', 'City', $application->city_entity ?? 'N/A'];
                $data[] = ['Entity Info', 'State', $application->state_entity ?? 'N/A'];
                $data[] = ['Entity Info', 'Postal Code', $application->postal_entity ?? 'N/A'];
                $data[] = ['Entity Socials', 'LinkedIn', $application->linkedin_entity ?? 'N/A'];
                $data[] = ['Entity Socials', 'Instagram', $application->instagram_entity ?? 'N/A'];
                $data[] = ['Entity Socials', 'Twitter', $application->twitter_entity ?? 'N/A'];
            }

            // Financial & Trading
            $data[] = ['Financial & Trading', 'Trading Purpose', $application->trading_purpose ?? 'N/A'];
            $data[] = ['Financial & Trading', 'Trading Purpose Details', $application->trading_purpose_desc ?? 'N/A'];
            $data[] = ['Financial & Trading', 'Flow of Funds', $application->flow_of_funds ?? 'N/A'];
            $data[] = ['Financial & Trading', 'First Trade Date', $application->first_trade_date ?? 'N/A'];
            $data[] = ['Financial & Trading', 'First Trade Size', ($application->first_trade_currency ?? '') . ' ' . ($application->first_trade_size ?? '')];
            $data[] = ['Financial & Trading', 'Est. Monthly Volume', ($application->monthly_volume_currency ?? '') . ' ' . ($application->monthly_volume_size ?? '')];
            $data[] = ['Financial & Trading', 'Source of Funding', $isCorporate ? ($application->source_funding_entity ?? 'N/A') : ($application->source_funding ?? 'N/A')];
            $data[] = ['Financial & Trading', 'Annual Income', ($application->annual_income_currency ?? '') . ' ' . ($application->annual_income_amount ?? '')];
            $data[] = ['Financial & Trading', 'Liquid Assets', ($application->liquid_assets_currency ?? '') . ' ' . ($application->liquid_assets_amount ?? '')];

            // Compliance & Declarations
            if (!$isCorporate) {
                $data[] = ['Compliance Questions', 'Declared Bankruptcy', ($application->declared_bankruptcy ?? 'No') . ' (' . ($application->declared_bankruptcy_desc ?? '') . ')'];
                $data[] = ['Compliance Questions', 'PEP Status', ($application->pep_status ?? 'No') . ' (' . ($application->pep_status_desc ?? '') . ')'];
                $data[] = ['Compliance Questions', 'High Value Transactions', $application->considerable_transactions ?? 'No'];
                $data[] = ['Compliance Questions', 'US Accreditations', $application->portfolio_excess ?? 'No'];
                $data[] = ['Compliance Questions', 'Accredited Investor', $application->accredited_investor ?? 'No'];
            } else {
                $data[] = ['Compliance Questions', 'Declared Bankruptcy', ($application->declared_bankruptcy_entity ?? 'No') . ' (' . ($application->declared_bankruptcy_entity_desc ?? '') . ')'];
                $data[] = ['Compliance Questions', 'PEP Status', ($application->pep_status_entity ?? 'No') . ' (' . ($application->pep_status_entity_desc ?? '') . ')'];
                $data[] = ['Compliance Questions', 'US Financial Entity', $application->financial_entity_us ?? 'No'];
                $data[] = ['Compliance Questions', 'Swap Dealer', $application->swap_dealer ?? 'No'];
            }
            $data[] = ['Compliance Questions', 'Declaration Signed', $application->declaration_signed ? 'Yes' : 'No'];

            // Bank Details
            $data[] = ['Bank Coordinates', 'Beneficiary Bank', $application->bank_name ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Bank Address', $application->bank_address ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Bank Country', $application->bank_country ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Account Holder', $application->bank_account_holder ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Account Number', $application->bank_account_number ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Routing Code', $application->bank_routing_code ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'SWIFT/BIC Code', $application->bank_swift ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Account Currency', $application->bank_currency ?? 'USD'];
            $data[] = ['Bank Coordinates', 'Beneficiary Address', $application->bank_beneficiary_address ?? 'N/A'];
            $data[] = ['Bank Coordinates', 'Intermediary Bank', $application->bank_intermediary ?? 'N/A'];

            // Uploaded Documents References
            if (!$isCorporate) {
                $kycFiles = $parseFiles($application->kyc_document_file);
                if (empty($kycFiles)) {
                    $data[] = ['Uploaded Document Names', 'KYC Document File', 'N/A'];
                } else {
                    foreach ($kycFiles as $idx => $f) {
                        $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                        $data[] = ['Uploaded Document Names', 'KYC Document File [' . ($idx + 1) . ']', $fileUrl];
                    }
                }

                $fundsFiles = $parseFiles($application->proof_funds_file);
                if (empty($fundsFiles)) {
                    $data[] = ['Uploaded Document Names', 'Proof of Funds File', 'N/A'];
                } else {
                    foreach ($fundsFiles as $idx => $f) {
                        $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                        $data[] = ['Uploaded Document Names', 'Proof of Funds File [' . ($idx + 1) . ']', $fileUrl];
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
                        $data[] = ['Uploaded Document Names', $docLabel, 'N/A'];
                    } else {
                        foreach ($files as $idx => $f) {
                            $fileUrl = $protocol . $host . url('/uploads/' . rawurlencode($f));
                            $data[] = ['Uploaded Document Names', $docLabel . ' [' . ($idx + 1) . ']', $fileUrl];
                        }
                    }
                }
            }
        }

        $output = fopen('php://temp', 'r+');
        foreach ($data as $row) {
            fputcsv($output, $row, ",", "\"", "\\");
        }
        rewind($output);
        $csvString = stream_get_contents($output);
        fclose($output);

        return $csvString;
    }
}
