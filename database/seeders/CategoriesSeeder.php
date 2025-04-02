<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();
        $data = [
            "Interconnection"=>["Revenue sharing calculation", "QoS reporting", "VAT collection", "Calling pulse", "BTRC approval", "Connection mode", "Disputes", "Agreement reporting", "Traffic data retention and analysis", "Interconnection info share", "Quality of Service", "Charging", "NIX", "Revenue receive", "Competition activities", "Copyright", "Crisis Management", "Customer Service", "Data retention", "Distributor/Retailer", "Fraud/ crime", "Geographic Information Services (GIS) Map", "Human Resource", "Import", "Information security", "Infrastructure", "International Gateway (IGW)", "International Internet Gateway (IIG)", "International Private Leased Circuit (IPLC)", "Internet of things (IoT)", "Internet service provider (ISP)", "License", "Lottery", "MNP", "Network Monitoring System", "Others", "Panic button", "Parental guidance", "Roaming", "Security", "Short Code", "SIM box/ VoIP", "SMS", "VPN", "Service (DOB Payment)", "Tower sharing"],
            "m-centrex"=>["Information access", "Name format", "VAT"],
            "NTTN"=>["Infrastructures reporting in website", "Sell/ Sublease", "Agreement", "Connection transmission", "Reporting"],
            "Number Plan"=>["Utilization plan reporting", "Utilization reporting", "BTRC approval", "Purpose of assignment", "Utilization of numbers", "Issuance of numbers", "Identification information", "Call origination/termination", "Suspension or cancellation", "Service", "Number structure", "Reporting", "Data retention"],
            "Online recharge"=>["Information availability", "Charging scope", "Issuance of numbers", "Identification information", "Call origination/termination", "Suspension or cancellation", "Service", "Number structure", "Reporting", "Data retention"],
            "Payment"=>["Annual License Fee", "Annual spectrum fees", "Spectrum charges calculation", "Revenue sharing", "Social Obligation Fund", "Timeframe", "Frequency", "AIT", "Payment", "Revenue Sharing", "Revenue sharing calculation", "05. Revenue sharing late fee", "10. Reconciliation of payment", "Spectrum charge and revenue sharing", "VAT", "Bank Transactions"],
            "QoS"=>["Call drop", "Call drop reporting", "Compliance Reporting", "Parameter Reporting", "QoS reporting", "benchmark calculation", "Benchmark", "Data Speed", "Drive test", "Information retention", "Indicative contention ratio", "Service quality"],
            "Reporting"=>["Annual financial records reporting", "Complaints reporting", "CSR reporting", "Customer and Equipment information reporting [Monthly]", "Detected and barred numbers reporting", "Mobile devices percentage reporting [Quarterly]", "New services information reporting", "SIM resale information", "WiFi details reporting", "Equipment", "Links reporting"],
            "Rollout"=>["Rollout reporting", "Connectivity", "Consent", "Extention of network", "Phase", "Service coverage"],
            "Sim registration/ recycle"=>["Deactivated SIMs/RUIMs reporting", "Seized SIMs reporting", "Directive priority", "Biometric device share", "SIM /RUIM Registration", "SIM /RUIM biometric registration", "SIM/RUIM replacement", "SIM resale", "SIM/RUIM recycle duration", "SIM/ RUIM de-registration", "Compliances for Teknaf and Ukhia", "Contract", "Corporate SIM", "Corporate SIM data retention", "Corporate SIM/RUIM registration", "Migration", "Sim recycle duration", "Sim reuse advertisement", "Sim reuse price"],
            "Frequency"=>["Frequency reporting"],
            "Spectrum/ Frequency"=>["Spectrum charges calculation", "Bandwidth allocation", "BRTC Approval", "Capacity", "Danger signal", "Equipment", "Frequency of radio spectrum", "Gregorian Calendar", "Transfer of spectrum", "Use of Spectrum"],
            "Toll Free"=>["LTFS numbers reporting", "BTRC approval", "Configuration", "Data retention", "Fees", "Reallocation duration"],
            "VAS"=>["Revenue Sharing", "QOS", "Revenue Sharing", "Service halt", "SMS based promotion"],
            "VAT"=>["VAT charging"],
            "VTS"=>["Fees", "Revenue Sharing", "GPS device Reporting", "Revenue Sharing calculation", "Service Reporting", "Agreement", "BTRC approval", "Pricing", "Privacy and Confidentiality", "Safety concerns", "SIM registration", "VTS changeability"],
            "Mobile Financial Services (MFS)"=>["Charging scope", "Tariff", "BTRC approval", "Agreement vetting", "Guideline", "Transaction", "BKash service for GP subscribers", "Mobile financial service with Dutch Bangla Bank Ltd (DBBL) for Grameenphone scriber", "Data retention", "Disputes", "Contracts and related documents reporting"],
            "SMP"=>["Interconnection", "Voice tariff", "Anti-market activities", "BTRC approval", "MNP"],
            "Tariff"=>["Charging Intervals and Charging Units", "service tariff", "Tariff consideration", "Billing and Metering", "BTRC approval", "Business report and Information Reporting", "Charging", "Charging Dynamic", "Cost Model", "Customer Billing", "Data", "Data speed", "Data volume", "Data: Information Availity", "Data: Pay per use", "Data: Push-Notification for Charging -", "Homogenous charging", "Information and Procedures", "Intimation for services and offers", "ISD call", "Market communication", "Migration charging", "Notification", "Packages", "Promotional offer", "Push-Notifications", "Record keeping", "Service category", "Service expiry", "Service Opted-in/Opted-out", "Usage notification", "Validity", "Validity and usability of services", "VAT"],
            "Borderbase transceiver station (BTS)"=>["BTRC approval", "Requirements for application", "Requirements for coverage", "BTS Infrastructure sharing", "Reporting", "National security", "Service coverage"],
            "Call Center"=>["BTRC Approval", "Impact of outsourcing"],
            "Call Detailed Record (CDR)"=>["CDR info reporting to customer", "Data preservation", "Monitoring System", "Information share"],
            "Competition activities"=>["Anti competitive activities", "Unfair Competition activities", "Discrimination activities"],
            "Copyright"=>["Requirement for Copyright", "Requirement for intellectual rights"],
            "Crisis Management"=>["Responses to emergency situation", "Notification to BTRC"],
            "Customer Service"=>["Approval from customer", "Standard contract and Commission approval", "Compliances for Complaints and Consumer Protection", "Requirements for call forwarding", "Requirements for connection expiry", "Data retention", "SMS timing"],
            "Data retention"=>["Registration Information", "All data and relevant documents", "Storage capacity", "Information retention", "Data retention", "SMS timing"],
            "Distributor/Retailer"=>["Distributor/ retailer On-boarding", "Maintain list for distribution of prepaid cards"],
            "Fraud/ crime"=>["SMS/MMS/TVR/USSD based ·Fraudulent Activities", "Telecom Fraud Management", "Data retention", "SMS timing"],
            "Geographic Information Services (GIS) Map"=>["GIS map compliances"],
            "Human Resource"=>["Employment compliances", "Information retention"],
            "Import"=>["Compliances after getting the import NOC"],
            "Information security"=>["Information Confidentiality", "Data protection"],
            "Infrastructure"=>["BTRC approval","BTS Infrastructure sharing"],
            "International Gateway (IGW)"=>["Revenue sharing"],
            "International Internet Gateway (IIG)"=>["IIG connection"],
            "International Private Leased Circuit (IPLC)"=>["Terms and conditions"],
            "Internet of things (IoT)"=>["Output limit", "Use guideline"],
            "Internet service provider (ISP)"=>["VSAT use"],
            "License"=>["BTRC approval", "BTRC approval for changes in Share Capital", "Transfer"],
            "Lottery"=>["Agreement reporting", "Tariff", "SMS notification"],
            "MNP"=>["Customer billing", "Details of Porting Implementation", "Flow of Information and Exchange of Notifications", "Operational process", "Rules for Batch Processing"],
            "Network Monitoring System"=>["Centralized monitoring system", "illegal call termination", "LI COMPLIANCE"],
            "Others"=>["BTRC approval", "Call Blocking", "Code of Commercial Practice", "Connectivity", "Health and Environmental Hazards", "Health Safety", "IMEI", "Network", "Privacy and Confidentiality", "Radio communications"],
            "Panic button"=>["Tariff rate", "Conditions for Panic button"],
            "Parental guidance"=>["Tariff rate", "Conditions for Panic button"],
            "Roaming"=>["Call route"],
            "Security"=>["Live API", "National Security"],
            "Short Code"=>["Terms and Conditions"],
            "SIM box/ VoIP"=>["Refund", "SIM Box"],
            "SMS"=>["Advertisement", "BTRC approval", "Charge", "Language", "Standard keywords", "Unwanted SMS"],
            "VPN"=>["Use"],
            "Service (DOB Payment)"=>["Reporting to BTRC"],
            "Tower sharing"=>["Tower rollback", "Tower building", "Tower sharing request and reporting", "Tower sharing and reporting"]];

        foreach ($data as $category => $subCategories) {
            $id = Category::create(['name' => $category])->id;
            foreach ($subCategories as $subCategory) {
                Category::create([
                    'parent_id' => $id,
                    'name' => $subCategory
                ]);
            }
        }
    }
}

