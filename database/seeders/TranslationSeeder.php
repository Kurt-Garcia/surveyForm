<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing translations
        Translation::truncate();
        
        // Define all translations
        $translations = [
            // English translations
            ['key' => 'account_name', 'locale' => 'en', 'value' => 'Account Name', 'group' => 'survey'],
            ['key' => 'account_type', 'locale' => 'en', 'value' => 'Account Type', 'group' => 'survey'],
            ['key' => 'date', 'locale' => 'en', 'value' => 'Date', 'group' => 'survey'],
            ['key' => 'satisfaction_level', 'locale' => 'en', 'value' => 'Satisfaction Level', 'group' => 'survey'],
            ['key' => 'required', 'locale' => 'en', 'value' => 'Required', 'group' => 'survey'],
            ['key' => 'optional', 'locale' => 'en', 'value' => 'Optional', 'group' => 'survey'],
            ['key' => 'recommendation', 'locale' => 'en', 'value' => 'Recommendation', 'group' => 'survey'],
            ['key' => 'recommendation_question', 'locale' => 'en', 'value' => 'How likely is it that you would recommend our company to a friend or colleague?', 'group' => 'survey'],
            ['key' => 'select_rating', 'locale' => 'en', 'value' => 'Select a rating', 'group' => 'survey'],
            ['key' => 'improvement_areas', 'locale' => 'en', 'value' => 'Areas for Improvement Suggestions', 'group' => 'survey'],
            ['key' => 'select_all_apply', 'locale' => 'en', 'value' => 'Select all that apply:', 'group' => 'survey'],
            ['key' => 'product_quality', 'locale' => 'en', 'value' => 'Product / Service Quality', 'group' => 'survey'],
            ['key' => 'delivery_logistics', 'locale' => 'en', 'value' => 'Delivery & Logistics', 'group' => 'survey'],
            ['key' => 'customer_service', 'locale' => 'en', 'value' => 'Sales & Customer Service', 'group' => 'survey'],
            ['key' => 'timeliness', 'locale' => 'en', 'value' => 'Timeliness', 'group' => 'survey'],
            ['key' => 'returns_handling', 'locale' => 'en', 'value' => 'Returns / BO Handling', 'group' => 'survey'],
            ['key' => 'others', 'locale' => 'en', 'value' => 'Others (please specify)', 'group' => 'survey'],
            ['key' => 'others_placeholder', 'locale' => 'en', 'value' => 'Please specify other areas for improvement...', 'group' => 'survey'],
            ['key' => 'submit_survey', 'locale' => 'en', 'value' => 'Submit Survey', 'group' => 'survey'],
            ['key' => 'thank_you', 'locale' => 'en', 'value' => 'THANK YOU!', 'group' => 'survey'],
            ['key' => 'thank_you_message', 'locale' => 'en', 'value' => 'WE APPRECIATE YOUR FEEDBACK!', 'group' => 'survey'],
            ['key' => 'feedback_helps', 'locale' => 'en', 'value' => 'Your input helps us serve you better.', 'group' => 'survey'],
            ['key' => 'view_response', 'locale' => 'en', 'value' => 'View Response', 'group' => 'survey'],
            ['key' => 'rating_poor', 'locale' => 'en', 'value' => '1 - Poor', 'group' => 'survey'],
            ['key' => 'rating_needs_improvement', 'locale' => 'en', 'value' => '2 - Needs Improvement', 'group' => 'survey'],
            ['key' => 'rating_satisfactory', 'locale' => 'en', 'value' => '3 - Satisfactory', 'group' => 'survey'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'en', 'value' => '4 - Very Satisfactory', 'group' => 'survey'],
            ['key' => 'rating_excellent', 'locale' => 'en', 'value' => '5 - Excellent', 'group' => 'survey'],
            ['key' => 'validation_alert', 'locale' => 'en', 'value' => 'Please Fill In All Required Fields!', 'group' => 'survey'],
            
            // Consent form translations
            ['key' => 'consent_title', 'locale' => 'en', 'value' => 'Survey Consent Statement', 'group' => 'survey'],
            ['key' => 'consent_subtitle', 'locale' => 'en', 'value' => 'Customer Satisfaction Survey', 'group' => 'survey'],
            ['key' => 'consent_dear_customer', 'locale' => 'en', 'value' => 'Dear Valued Customer,', 'group' => 'survey'],
            ['key' => 'consent_intro', 'locale' => 'en', 'value' => 'We appreciate your participation in this customer satisfaction survey and your willingness to share your thoughts. Your insights will assist us in enhancing our services and client satisfaction.', 'group' => 'survey'],
            ['key' => 'consent_terms_intro', 'locale' => 'en', 'value' => 'By completing this survey, you acknowledge and agree to the following terms:', 'group' => 'survey'],
            ['key' => 'consent_question', 'locale' => 'en', 'value' => 'Please indicate your consent to proceed with the survey:', 'group' => 'survey'],
            ['key' => 'consent_accept', 'locale' => 'en', 'value' => 'I accept the terms and conditions and consent to participate', 'group' => 'survey'],
            ['key' => 'consent_decline', 'locale' => 'en', 'value' => 'I do not accept the terms and conditions', 'group' => 'survey'],
            ['key' => 'consent_continue', 'locale' => 'en', 'value' => 'Continue to Survey', 'group' => 'survey'],
            ['key' => 'consent_footer_note', 'locale' => 'en', 'value' => 'Thank you for your valuable time and feedback.', 'group' => 'survey'],
            ['key' => 'consent_voluntary', 'locale' => 'en', 'value' => 'Voluntary Participation', 'group' => 'survey'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'en', 'value' => 'Your participation in this survey is entirely voluntary. You are not required to answer all questions, and you may withdraw at any time without penalty.', 'group' => 'survey'],
            ['key' => 'consent_purpose', 'locale' => 'en', 'value' => 'Purpose of Survey', 'group' => 'survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'en', 'value' => 'The information you provide will be used solely for the purpose of improving our services and understanding customer satisfaction levels.', 'group' => 'survey'],
            ['key' => 'consent_personal_info', 'locale' => 'en', 'value' => 'Personal Information', 'group' => 'survey'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'en', 'value' => 'We will collect only the information necessary for this survey. Your personal details will be kept strictly confidential and will not be shared with third parties.', 'group' => 'survey'],
            ['key' => 'consent_confidentiality', 'locale' => 'en', 'value' => 'Confidentiality', 'group' => 'survey'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'en', 'value' => 'All responses will be kept confidential and anonymous. Individual responses will not be disclosed to anyone outside our organization.', 'group' => 'survey'],
            ['key' => 'consent_data_protection', 'locale' => 'en', 'value' => 'Data Protection', 'group' => 'survey'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'en', 'value' => 'Your data will be stored securely and will be used only for the stated purposes. We comply with applicable data protection regulations.', 'group' => 'survey'],
            
            // Language selection translations
            ['key' => 'language_selection_title', 'locale' => 'en', 'value' => 'Language Selection', 'group' => 'survey'],
            ['key' => 'language_selection_subtitle', 'locale' => 'en', 'value' => 'Please select your preferred language for the survey', 'group' => 'survey'],
            ['key' => 'language_selection_continue', 'locale' => 'en', 'value' => 'Continue', 'group' => 'survey'],
            ['key' => 'language_selection_note', 'locale' => 'en', 'value' => 'You can change the language at any time during the survey.', 'group' => 'survey'],
            ['key' => 'language_english', 'locale' => 'en', 'value' => 'English', 'group' => 'survey'],
            ['key' => 'language_tagalog', 'locale' => 'en', 'value' => 'Tagalog', 'group' => 'survey'],
            ['key' => 'language_cebuano', 'locale' => 'en', 'value' => 'Cebuano', 'group' => 'survey'],
            
            // Improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'en', 'value' => 'We hope products are always available. Some items are often out of stock.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'en', 'value' => 'Please monitor product expiration dates more carefully. We sometimes receive items that are near expiry.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'en', 'value' => 'Some products arrive with dents, leaks, or damaged packaging. Kindly ensure all items are in good condition.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'en', 'value' => 'We\'d appreciate it if deliveries consistently arrive on time, as promised.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'en', 'value' => 'There have been a few instances of missing items in our deliveries. Please double-check orders for completeness.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'en', 'value' => 'It would be helpful if our concerns or follow-ups were responded to more quickly.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'en', 'value' => 'We appreciate clear communication. Kindly ensure that all interactions remain polite and professional.', 'group' => 'survey'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'en', 'value' => 'Please try to follow the agreed delivery or visit schedule to avoid disruptions in our store operations.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'en', 'value' => 'I hope the return process can be made quicker and more convenient.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'en', 'value' => 'Please improve coordination when it comes to picking up bad order items.', 'group' => 'survey'],
            
            // Tagalog translations
            ['key' => 'account_name', 'locale' => 'tl', 'value' => 'Pangalan ng Account', 'group' => 'survey'],
            ['key' => 'account_type', 'locale' => 'tl', 'value' => 'Uri ng Account', 'group' => 'survey'],
            ['key' => 'date', 'locale' => 'tl', 'value' => 'Petsa', 'group' => 'survey'],
            ['key' => 'satisfaction_level', 'locale' => 'tl', 'value' => 'Antas ng Kasiyahan', 'group' => 'survey'],
            ['key' => 'required', 'locale' => 'tl', 'value' => 'Kailangan', 'group' => 'survey'],
            ['key' => 'optional', 'locale' => 'tl', 'value' => 'Opsyonal', 'group' => 'survey'],
            ['key' => 'recommendation', 'locale' => 'tl', 'value' => 'Rekomendasyon', 'group' => 'survey'],
            ['key' => 'recommendation_question', 'locale' => 'tl', 'value' => 'Gaano ka malamang na irerekumenda mo ang aming kumpanya sa iyong kaibigan o kasamahan?', 'group' => 'survey'],
            ['key' => 'select_rating', 'locale' => 'tl', 'value' => 'Pumili ng rating', 'group' => 'survey'],
            ['key' => 'improvement_areas', 'locale' => 'tl', 'value' => 'Mga Lugar para sa Pagpapabuti ng mga Mungkahi', 'group' => 'survey'],
            ['key' => 'select_all_apply', 'locale' => 'tl', 'value' => 'Piliin lahat na naaangkop:', 'group' => 'survey'],
            ['key' => 'product_quality', 'locale' => 'tl', 'value' => 'Kalidad ng Produkto / Serbisyo', 'group' => 'survey'],
            ['key' => 'delivery_logistics', 'locale' => 'tl', 'value' => 'Paghahatid at Logistics', 'group' => 'survey'],
            ['key' => 'customer_service', 'locale' => 'tl', 'value' => 'Benta at Customer Service', 'group' => 'survey'],
            ['key' => 'timeliness', 'locale' => 'tl', 'value' => 'Pagkamaagap', 'group' => 'survey'],
            ['key' => 'returns_handling', 'locale' => 'tl', 'value' => 'Pag-handle ng Returns / BO', 'group' => 'survey'],
            ['key' => 'others', 'locale' => 'tl', 'value' => 'Iba pa (pakitukoy)', 'group' => 'survey'],
            ['key' => 'others_placeholder', 'locale' => 'tl', 'value' => 'Pakitukoy ang iba pang mga lugar para sa pagpapabuti...', 'group' => 'survey'],
            ['key' => 'submit_survey', 'locale' => 'tl', 'value' => 'Isumite ang Survey', 'group' => 'survey'],
            ['key' => 'thank_you', 'locale' => 'tl', 'value' => 'SALAMAT!', 'group' => 'survey'],
            ['key' => 'thank_you_message', 'locale' => 'tl', 'value' => 'PINASASALAMATAN NAMIN ANG INYONG FEEDBACK!', 'group' => 'survey'],
            ['key' => 'feedback_helps', 'locale' => 'tl', 'value' => 'Ang inyong input ay tumutulong sa amin na magbigay ng mas magandang serbisyo.', 'group' => 'survey'],
            ['key' => 'view_response', 'locale' => 'tl', 'value' => 'Tingnan ang Sagot', 'group' => 'survey'],
            ['key' => 'rating_poor', 'locale' => 'tl', 'value' => '1 - Mahina', 'group' => 'survey'],
            ['key' => 'rating_needs_improvement', 'locale' => 'tl', 'value' => '2 - Kailangan ng Pagpapabuti', 'group' => 'survey'],
            ['key' => 'rating_satisfactory', 'locale' => 'tl', 'value' => '3 - Kasiya-siya', 'group' => 'survey'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'tl', 'value' => '4 - Napakasiya-siya', 'group' => 'survey'],
            ['key' => 'rating_excellent', 'locale' => 'tl', 'value' => '5 - Napakagaling', 'group' => 'survey'],
            ['key' => 'validation_alert', 'locale' => 'tl', 'value' => 'Pakipunan ang lahat ng kailangang mga patlang!', 'group' => 'survey'],
            
            // Tagalog consent form translations
            ['key' => 'consent_title', 'locale' => 'tl', 'value' => 'Pahayag ng Pagpapahintulot sa Survey', 'group' => 'survey'],
            ['key' => 'consent_subtitle', 'locale' => 'tl', 'value' => 'Survey ng Kasiyahan ng Customer', 'group' => 'survey'],
            ['key' => 'consent_dear_customer', 'locale' => 'tl', 'value' => 'Minamahal na Customer,', 'group' => 'survey'],
            ['key' => 'consent_intro', 'locale' => 'tl', 'value' => 'Pinasasalamatan namin ang inyong partisipasyon sa customer satisfaction survey na ito at ang inyong pagkahandang magbahagi ng inyong mga kaisipan. Ang inyong mga puna ay makakatulong sa amin na mapabuti ang aming mga serbisyo at kasiyahan ng mga kliyente.', 'group' => 'survey'],
            ['key' => 'consent_terms_intro', 'locale' => 'tl', 'value' => 'Sa pamamagitan ng pagkumpleto ng survey na ito, kinikilala at sumasang-ayon kayo sa mga sumusunod na tuntunin:', 'group' => 'survey'],
            ['key' => 'consent_question', 'locale' => 'tl', 'value' => 'Pakiipahayag ang inyong pahintulot na magpatuloy sa survey:', 'group' => 'survey'],
            ['key' => 'consent_accept', 'locale' => 'tl', 'value' => 'Tinatanggap ko ang mga tuntunin at kondisyon at pumapayag na lumahok', 'group' => 'survey'],
            ['key' => 'consent_decline', 'locale' => 'tl', 'value' => 'Hindi ko tinatanggap ang mga tuntunin at kondisyon', 'group' => 'survey'],
            ['key' => 'consent_continue', 'locale' => 'tl', 'value' => 'Magpatuloy sa Survey', 'group' => 'survey'],
            ['key' => 'consent_footer_note', 'locale' => 'tl', 'value' => 'Salamat sa inyong mahalalagang oras at feedback.', 'group' => 'survey'],
            ['key' => 'consent_voluntary', 'locale' => 'tl', 'value' => 'Kusang Paglahok', 'group' => 'survey'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'tl', 'value' => 'Ang inyong paglahok sa survey na ito ay lubos na kusang-loob. Hindi kayo obligadong sagutin ang lahat ng mga katanungan, at maaari kayong umalis anumang oras nang walang parusa.', 'group' => 'survey'],
            ['key' => 'consent_purpose', 'locale' => 'tl', 'value' => 'Layunin ng Survey', 'group' => 'survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'tl', 'value' => 'Ang impormasyong inyong ibibigay ay gagamitin lamang para sa layuning mapabuti ang aming mga serbisyo at maunawaan ang mga antas ng kasiyahan ng customer.', 'group' => 'survey'],
            ['key' => 'consent_personal_info', 'locale' => 'tl', 'value' => 'Personal na Impormasyon', 'group' => 'survey'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'tl', 'value' => 'Kokolektahin namin lamang ang impormasyong kinakailangan para sa survey na ito. Ang inyong mga personal na detalye ay magiging tuwiran at hindi ibabahagi sa ibang mga partido.', 'group' => 'survey'],
            ['key' => 'consent_confidentiality', 'locale' => 'tl', 'value' => 'Pagiging Kumpidensyal', 'group' => 'survey'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'tl', 'value' => 'Lahat ng mga sagot ay mananatiling kumpidensyal at anonymous. Ang mga indibidwal na sagot ay hindi ibubunyag sa sinuman sa labas ng aming organisasyon.', 'group' => 'survey'],
            ['key' => 'consent_data_protection', 'locale' => 'tl', 'value' => 'Proteksyon ng Data', 'group' => 'survey'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'tl', 'value' => 'Ang inyong data ay itatago nang secure at gagamitin lamang para sa mga nabanggit na layunin. Sumusunod kami sa mga naaangkop na regulasyon sa proteksyon ng data.', 'group' => 'survey'],
            
            // Tagalog language selection translations
            ['key' => 'language_selection_title', 'locale' => 'tl', 'value' => 'Pagpili ng Wika', 'group' => 'survey'],
            ['key' => 'language_selection_subtitle', 'locale' => 'tl', 'value' => 'Pakipili ang inyong gustong wika para sa survey', 'group' => 'survey'],
            ['key' => 'language_selection_continue', 'locale' => 'tl', 'value' => 'Magpatuloy', 'group' => 'survey'],
            ['key' => 'language_selection_note', 'locale' => 'tl', 'value' => 'Maaari ninyong baguhin ang wika anumang oras sa survey.', 'group' => 'survey'],
            ['key' => 'language_english', 'locale' => 'tl', 'value' => 'English', 'group' => 'survey'],
            ['key' => 'language_tagalog', 'locale' => 'tl', 'value' => 'Tagalog', 'group' => 'survey'],
            ['key' => 'language_cebuano', 'locale' => 'tl', 'value' => 'Cebuano', 'group' => 'survey'],
            
            // Tagalog improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'tl', 'value' => 'Umaasa kaming laging available ang mga produkto. Madalas na out of stock ang ilang items.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'tl', 'value' => 'Pakibantayan nang mas maingat ang mga expiration date ng produkto. Minsan nakakakuha kami ng mga item na malapit nang mag-expire.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'tl', 'value' => 'Ang ilang produkto ay dumarating na may mga dents, leaks, o sirang packaging. Pakitiyak na lahat ng items ay nasa magandang kondisyon.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'tl', 'value' => 'Maappreciate namin kung palagi na lang on time ang mga delivery, gaya ng pangako.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'tl', 'value' => 'May ilang pagkakataon na may mga missing items sa aming deliveries. Pakidouble-check ang orders para sa completeness.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'tl', 'value' => 'Makakatulong kung mas mabilis na matutugon ang aming mga concerns o follow-ups.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'tl', 'value' => 'Appreciate namin ang clear communication. Pakitiyak na lahat ng interactions ay nananatiling polite at professional.', 'group' => 'survey'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'tl', 'value' => 'Pakisubukan na sundin ang agreed delivery o visit schedule para maiwasan ang disruptions sa aming store operations.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'tl', 'value' => 'Sana mas mabilis at mas convenient ang return process.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'tl', 'value' => 'Pakiimprove ang coordination pagdating sa pagkuha ng bad order items.', 'group' => 'survey'],
            
            // Cebuano translations
            ['key' => 'account_name', 'locale' => 'ceb', 'value' => 'Ngalan sa Account', 'group' => 'survey'],
            ['key' => 'account_type', 'locale' => 'ceb', 'value' => 'Type sa Account', 'group' => 'survey'],
            ['key' => 'date', 'locale' => 'ceb', 'value' => 'Petsa', 'group' => 'survey'],
            ['key' => 'satisfaction_level', 'locale' => 'ceb', 'value' => 'Lebel sa Katagbawan', 'group' => 'survey'],
            ['key' => 'required', 'locale' => 'ceb', 'value' => 'Gikinahanglan', 'group' => 'survey'],
            ['key' => 'optional', 'locale' => 'ceb', 'value' => 'Opsyonal', 'group' => 'survey'],
            ['key' => 'recommendation', 'locale' => 'ceb', 'value' => 'Rekomendasyon', 'group' => 'survey'],
            ['key' => 'recommendation_question', 'locale' => 'ceb', 'value' => 'Unsa ka posible nga imong irekomenda ang among kompanya sa imong higala o kauban?', 'group' => 'survey'],
            ['key' => 'select_rating', 'locale' => 'ceb', 'value' => 'Pagpili og rating', 'group' => 'survey'],
            ['key' => 'improvement_areas', 'locale' => 'ceb', 'value' => 'Mga Lugar para sa Pagpauswag nga mga Sugyot', 'group' => 'survey'],
            ['key' => 'select_all_apply', 'locale' => 'ceb', 'value' => 'Pagpili sa tanan nga magamit:', 'group' => 'survey'],
            ['key' => 'product_quality', 'locale' => 'ceb', 'value' => 'Kalidad sa Produkto / Serbisyo', 'group' => 'survey'],
            ['key' => 'delivery_logistics', 'locale' => 'ceb', 'value' => 'Pagdala ug Logistics', 'group' => 'survey'],
            ['key' => 'customer_service', 'locale' => 'ceb', 'value' => 'Baligya ug Customer Service', 'group' => 'survey'],
            ['key' => 'timeliness', 'locale' => 'ceb', 'value' => 'Pagkamatuod sa Oras', 'group' => 'survey'],
            ['key' => 'returns_handling', 'locale' => 'ceb', 'value' => 'Pagdumala sa Returns / BO', 'group' => 'survey'],
            ['key' => 'others', 'locale' => 'ceb', 'value' => 'Uban pa (palihug tukya)', 'group' => 'survey'],
            ['key' => 'others_placeholder', 'locale' => 'ceb', 'value' => 'Palihug tukya ang ubang lugar para sa pagpauswag...', 'group' => 'survey'],
            ['key' => 'submit_survey', 'locale' => 'ceb', 'value' => 'Isumite ang Survey', 'group' => 'survey'],
            ['key' => 'thank_you', 'locale' => 'ceb', 'value' => 'SALAMAT!', 'group' => 'survey'],
            ['key' => 'thank_you_message', 'locale' => 'ceb', 'value' => 'GIPASALAMATAN NAMO ANG IMONG FEEDBACK!', 'group' => 'survey'],
            ['key' => 'feedback_helps', 'locale' => 'ceb', 'value' => 'Ang imong input nagtabang kanamo nga makahatag og mas maayong serbisyo.', 'group' => 'survey'],
            ['key' => 'view_response', 'locale' => 'ceb', 'value' => 'Tan-awa ang Tubag', 'group' => 'survey'],
            ['key' => 'rating_poor', 'locale' => 'ceb', 'value' => '1 - Dili Maayo', 'group' => 'survey'],
            ['key' => 'rating_needs_improvement', 'locale' => 'ceb', 'value' => '2 - Kinahanglan og Pagpauswag', 'group' => 'survey'],
            ['key' => 'rating_satisfactory', 'locale' => 'ceb', 'value' => '3 - Maayo', 'group' => 'survey'],
            ['key' => 'rating_very_satisfactory', 'locale' => 'ceb', 'value' => '4 - Maayo Kaayo', 'group' => 'survey'],
            ['key' => 'rating_excellent', 'locale' => 'ceb', 'value' => '5 - Perpekto', 'group' => 'survey'],
            ['key' => 'validation_alert', 'locale' => 'ceb', 'value' => 'Palihug pun-a ang tanan nga gikinahanglan nga mga patlang!', 'group' => 'survey'],
            
            // Cebuano consent form translations
            ['key' => 'consent_title', 'locale' => 'ceb', 'value' => 'Pahayag sa Pagpahintulot sa Survey', 'group' => 'survey'],
            ['key' => 'consent_subtitle', 'locale' => 'ceb', 'value' => 'Survey sa Katagbawan sa Customer', 'group' => 'survey'],
            ['key' => 'consent_dear_customer', 'locale' => 'ceb', 'value' => 'Minahal nga Customer,', 'group' => 'survey'],
            ['key' => 'consent_intro', 'locale' => 'ceb', 'value' => 'Gipasalamatan namo ang imong partisipasyon niini nga customer satisfaction survey ug ang imong kaandam sa pagpaambit sa imong mga hunahuna. Ang imong mga panabut motabang kanamo sa pagpauswag sa among mga serbisyo ug kasagaran sa mga kliyente.', 'group' => 'survey'],
            ['key' => 'consent_terms_intro', 'locale' => 'ceb', 'value' => 'Pinaagi sa pagtapos niini nga survey, giila ug giuyon nimo ang mosunod nga mga termino:', 'group' => 'survey'],
            ['key' => 'consent_question', 'locale' => 'ceb', 'value' => 'Palihug ipakita ang imong consent sa pagpadayon sa survey:', 'group' => 'survey'],
            ['key' => 'consent_accept', 'locale' => 'ceb', 'value' => 'Gidawat nako ang mga termino ug kondisyon ug gusto kong magpadayon sa survey', 'group' => 'survey'],
            ['key' => 'consent_decline', 'locale' => 'ceb', 'value' => 'Wala nako gidawat ang mga termino ug kondisyon', 'group' => 'survey'],
            ['key' => 'consent_continue', 'locale' => 'ceb', 'value' => 'Padayon sa Survey', 'group' => 'survey'],
            ['key' => 'consent_footer_note', 'locale' => 'ceb', 'value' => 'Salamat sa imong bililhong oras ug feedback.', 'group' => 'survey'],
            ['key' => 'consent_voluntary', 'locale' => 'ceb', 'value' => 'Boluntaryong Pag-apil', 'group' => 'survey'],
            ['key' => 'consent_voluntary_desc', 'locale' => 'ceb', 'value' => 'Ang imong pag-apil niini nga survey hingpit nga boluntaryo. Dili ka obligado sa pagtubag sa tanang mga pangutana, ug mahimo ka mobiya bisan unsa nga oras nga walay silot.', 'group' => 'survey'],
            ['key' => 'consent_purpose', 'locale' => 'ceb', 'value' => 'Katuyoan sa Survey', 'group' => 'survey'],
            ['key' => 'consent_purpose_desc', 'locale' => 'ceb', 'value' => 'Ang impormasyon nga imong ihatag gamiton lamang para sa katuyoan sa pagpauswag sa among mga serbisyo ug pagsabot sa mga lebel sa kasagaran sa customer.', 'group' => 'survey'],
            ['key' => 'consent_personal_info', 'locale' => 'ceb', 'value' => 'Personal nga Impormasyon', 'group' => 'survey'],
            ['key' => 'consent_personal_info_desc', 'locale' => 'ceb', 'value' => 'Kolektahon namo lamang ang impormasyon nga gikinahanglan para niini nga survey. Ang imong mga personal nga detalye magpabilin nga tago-tago ug dili ipaambit sa laing mga partido.', 'group' => 'survey'],
            ['key' => 'consent_confidentiality', 'locale' => 'ceb', 'value' => 'Pagiging Kompidensyal', 'group' => 'survey'],
            ['key' => 'consent_confidentiality_desc', 'locale' => 'ceb', 'value' => 'Ang tanang mga tubag magpabilin nga kompidensyal ug anonymous. Ang mga indibidwal nga tubag dili ibutyag sa bisan kinsa sa gawas sa among organisasyon.', 'group' => 'survey'],
            ['key' => 'consent_data_protection', 'locale' => 'ceb', 'value' => 'Proteksyon sa Data', 'group' => 'survey'],
            ['key' => 'consent_data_protection_desc', 'locale' => 'ceb', 'value' => 'Ang imong data tipigan nga luwas ug gamiton lamang para sa mga gitaho nga mga katuyoan. Mosunod kami sa mga magamit nga regulasyon sa proteksyon sa data.', 'group' => 'survey'],
            
            // Cebuano language selection translations
            ['key' => 'language_selection_title', 'locale' => 'ceb', 'value' => 'Pagpili og Pinulongan', 'group' => 'survey'],
            ['key' => 'language_selection_subtitle', 'locale' => 'ceb', 'value' => 'Palihug pilia ang imong gusto nga pinulongan para sa survey', 'group' => 'survey'],
            ['key' => 'language_selection_continue', 'locale' => 'ceb', 'value' => 'Padayon', 'group' => 'survey'],
            ['key' => 'language_selection_note', 'locale' => 'ceb', 'value' => 'Mahimo nimong usbon ang pinulongan bisan unsa nga oras atol sa survey.', 'group' => 'survey'],
            ['key' => 'language_english', 'locale' => 'ceb', 'value' => 'English', 'group' => 'survey'],
            ['key' => 'language_tagalog', 'locale' => 'ceb', 'value' => 'Tagalog', 'group' => 'survey'],
            ['key' => 'language_cebuano', 'locale' => 'ceb', 'value' => 'Cebuano', 'group' => 'survey'],
            
            // Cebuano improvement details translations
            ['key' => 'improvement_details.product_quality.availability', 'locale' => 'ceb', 'value' => 'Naglaum kami nga ang mga produkto kanunay makuha. Ang pipila ka mga butang kanunay nga walay stock.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.expiration', 'locale' => 'ceb', 'value' => 'Palihug bantayi ang mga petsa sa pagkaexpire sa produkto nga mas maampingon. Usahay makadawat kami og mga butang nga hapit nang ma-expire.', 'group' => 'survey'],
            ['key' => 'improvement_details.product_quality.damage', 'locale' => 'ceb', 'value' => 'Ang pipila ka mga produkto moabot nga may mga dako, pagkatubo, o guba nga pagkabalot. Palihug siguruha nga ang tanan nga mga butang naa sa maayong kondisyon.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.on_time', 'locale' => 'ceb', 'value' => 'Mapasalamaton namo kung ang mga pagdala kanunay nga moabot sa hustong oras, sumala sa gisaad.', 'group' => 'survey'],
            ['key' => 'improvement_details.delivery_logistics.missing_items', 'locale' => 'ceb', 'value' => 'Adunay pipila ka mga higayon sa nawala nga mga butang sa among mga pagdala. Palihug susihon og maayo ang mga order para sa pagkakompleto.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.response_time', 'locale' => 'ceb', 'value' => 'Makatabang kung ang among mga kabalaka o mga follow-up matubag nga mas kusog.', 'group' => 'survey'],
            ['key' => 'improvement_details.customer_service.communication', 'locale' => 'ceb', 'value' => 'Gipasalamatan namo ang klaro nga komunikasyon. Palihug siguruha nga ang tanan nga mga pakig-uban magpadayon nga mabination ug propesyonal.', 'group' => 'survey'],
            ['key' => 'improvement_details.timeliness.schedule', 'locale' => 'ceb', 'value' => 'Palihug sulayi nga sundon ang nahisgutang iskhedyul sa pagdala o pagbisita aron malikayan ang mga pagkabalda sa among operasyon sa tindahan.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.return_process', 'locale' => 'ceb', 'value' => 'Naglaum ko nga ang proseso sa pagbalik mahimong mas paspas ug mas sayon.', 'group' => 'survey'],
            ['key' => 'improvement_details.returns_handling.bo_coordination', 'locale' => 'ceb', 'value' => 'Palihug pauswaga ang koordinasyon kung bahin sa pagkuha sa mga dautang order nga mga butang.', 'group' => 'survey'],
        ];
        
        // Insert translations in batches for better performance
        $batchSize = 100;
        $batches = array_chunk($translations, $batchSize);
        
        foreach ($batches as $batch) {
            Translation::insert($batch);
        }
        
        $this->command->info('Translation seeder completed successfully! Added ' . count($translations) . ' translations.');
    }
}
