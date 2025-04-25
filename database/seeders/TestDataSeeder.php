<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, disable foreign key checks so we can truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Delete existing data in the correct order (details first, then headers, etc.)
        SurveyResponseDetail::truncate();
        SurveyResponseHeader::truncate();
        SurveyQuestion::truncate();
        Survey::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Get the admin ID from the existing admin user
        $admin = Admin::first();
        
        if (!$admin) {
            $this->command->error('No admin found. Please seed admins first.');
            return;
        }
        
        // Array of realistic survey titles
        $surveyTitles = [
            'Customer Satisfaction Survey',
            'Product Feedback Form',
            'Service Quality Assessment',
            'Website Experience Evaluation',
            'Employee Engagement Survey',
            'Technical Support Feedback',
            'Training Program Evaluation',
            'Event Satisfaction Survey',
            'Application User Experience',
            'Annual Customer Feedback',
            'Restaurant Dining Experience',
            'Hotel Stay Satisfaction',
            'Banking Services Survey',
            'Healthcare Service Quality',
            'Retail Shopping Experience',
            'Software Usability Evaluation',
            'Conference Feedback Form',
            'Loan Application Experience',
            'Insurance Claims Process',
            'Mobile App User Feedback',
            'Course Evaluation Survey',
            'Transportation Services',
            'Tech Support Resolution',
            'E-commerce Checkout Process',
            'Online Learning Experience'
        ];
        
        // Arrays for generating realistic data
        $accountTypes = ['Corporate', 'Individual', 'Premium', 'Basic', 'Enterprise', 'Small Business', 'Non-profit', 'Government', 'Educational', 'Healthcare'];
        
        // Common questions for all surveys
        $commonQuestions = [
            [
                'text' => 'How would you rate our customer service?',
                'type' => 'radio',
                'required' => true,
            ],
            [
                'text' => 'How satisfied are you with the product quality?',
                'type' => 'star',
                'required' => true,
            ],
            [
                'text' => 'Was your issue resolved in a timely manner?',
                'type' => 'radio',
                'required' => true,
            ],
            [
                'text' => 'How would you rate the ease of use of our website?',
                'type' => 'radio',
                'required' => false,
            ],
            [
                'text' => 'How likely are you to recommend our services to others?',
                'type' => 'star',
                'required' => true,
            ],
            [
                'text' => 'Rate the professionalism of our staff.',
                'type' => 'star',
                'required' => false,
            ],
            [
                'text' => 'How would you rate the value for money of our products?',
                'type' => 'radio',
                'required' => true,
            ],
            [
                'text' => 'Rate the clarity of information provided to you.',
                'type' => 'radio',
                'required' => false,
            ]
        ];

        // Specialized questions by category
        $specializedQuestions = [
            // Restaurant specific questions
            'restaurant' => [
                [
                    'text' => 'How would you rate the food quality?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the cleanliness of our restaurant.',
                    'type' => 'radio',
                    'required' => false,
                ],
                [
                    'text' => 'How satisfied were you with the waiting time?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the atmosphere of our restaurant.',
                    'type' => 'radio',
                    'required' => false,
                ],
            ],
            // Hotel specific questions
            'hotel' => [
                [
                    'text' => 'How would you rate the room comfort?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the cleanliness of your room.',
                    'type' => 'radio',
                    'required' => true,
                ],
                [
                    'text' => 'How satisfied were you with the check-in/check-out process?',
                    'type' => 'star',
                    'required' => false,
                ],
                [
                    'text' => 'Rate the quality of amenities provided.',
                    'type' => 'radio',
                    'required' => false,
                ],
            ],
            // Software/App specific questions
            'software' => [
                [
                    'text' => 'How would you rate the app performance?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the intuitiveness of the user interface.',
                    'type' => 'radio',
                    'required' => true,
                ],
                [
                    'text' => 'How satisfied are you with the loading speed?',
                    'type' => 'star',
                    'required' => false,
                ],
                [
                    'text' => 'Rate the usefulness of the features.',
                    'type' => 'radio',
                    'required' => true,
                ],
            ],
            // Healthcare specific questions
            'healthcare' => [
                [
                    'text' => 'How would you rate the care provided by medical staff?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the cleanliness of the facility.',
                    'type' => 'radio',
                    'required' => true,
                ],
                [
                    'text' => 'How satisfied were you with waiting times?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the clarity of information about your treatment.',
                    'type' => 'radio',
                    'required' => false,
                ],
            ],
            // Education specific questions
            'education' => [
                [
                    'text' => 'How would you rate the quality of instruction?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the relevance of course materials.',
                    'type' => 'radio',
                    'required' => true,
                ],
                [
                    'text' => 'How satisfied were you with instructor support?',
                    'type' => 'star',
                    'required' => true,
                ],
                [
                    'text' => 'Rate the quality of learning resources provided.',
                    'type' => 'radio',
                    'required' => false,
                ],
            ],
        ];
        
        // Map survey titles to specialized question categories
        $surveyCategories = [
            'Restaurant Dining Experience' => 'restaurant',
            'Hotel Stay Satisfaction' => 'hotel',
            'Software Usability Evaluation' => 'software',
            'Mobile App User Feedback' => 'software',
            'Healthcare Service Quality' => 'healthcare',
            'Course Evaluation Survey' => 'education',
            'Online Learning Experience' => 'education',
        ];
        
        // Create realistic company names for account names
        $companyNames = [
            'Acme Corporation', 'Globex Industries', 'Initech Systems', 'Umbrella Corp', 
            'Stark Enterprises', 'Wayne Industries', 'LexCorp', 'Cyberdyne Systems',
            'Massive Dynamic', 'Soylent Corp', 'InGen Technologies', 'Weyland-Yutani',
            'Aperture Science', 'Tyrell Corporation', 'Oscorp Industries', 'Rekall Inc',
            'Monsters Inc', 'Buy n Large', 'Sirius Cybernetics', 'Wonka Industries',
            'Bluth Company', 'Los Pollos Hermanos', 'Dunder Mifflin', 'Sterling Cooper',
            'Prestige Worldwide', 'Virtucon', 'Oceanic Airlines', 'Ollivanders',
            'Gringotts Bank', 'Petronas Energy', 'Mooby Corp', 'Genco Olive Oil',
            'Spacely Sprockets', 'Very Big Corp', 'Nakatomi Trading', 'Paper Street Soap',
            'Clampett Oil', 'Orbit City Labs', 'Axe Capital', 'Hooli',
            'Pied Piper', 'Raviga Capital', 'Dundler Mifflin', 'Wernham Hogg',
            'Primatech Paper', 'Zorg Industries', 'Gekko & Co', 'Cybertron Tech',
            'Quantum Holdings', 'Monarch Solutions', 'Global Dynamics', 'Omni Consumer Products',
            'Terran Dominion', 'Venture Industries', 'Abstergo Industries', 'Veidt Enterprises',
            'Shinra Electric Power', 'Goliath National Bank', 'Stark Industries', 'Waystar Royco',
            'Hanso Foundation', 'Dyad Institute', 'Westworld', 'Weyland Corp',
            'Union Aerospace Corp', 'Umbrella Pharmaceuticals', 'Rossum Corporation', 'Prescott Industries'
        ];
        
        // Array of realistic comments
        $positiveComments = [
            'Very satisfied with the service and response time.',
            'The staff was extremely helpful. I would definitely use your services again.',
            'Product quality exceeded my expectations. Great job!',
            'I have been a customer for 5 years and have always received excellent service.',
            'Website was easy to navigate, found what I needed very quickly.',
            'The customer service team resolved my issue within minutes. Impressive!',
            'Best service I have received from any company in this industry.',
            'I appreciate how the team went above and beyond to meet my needs.',
            'Very professional and knowledgeable staff.',
            'Always prompt responses to my inquiries. Thank you!',
            'Will definitely recommend your services to colleagues.',
            'The quality of products is consistently high.',
            'Very intuitive interface, made my experience pleasant.',
            'Support team was excellent in addressing all my concerns.',
            'I value the attention to detail your team provides.',
            'Exceptional service from start to finish.',
            'The staff was courteous and effective at solving my issue.',
            'Your team\'s expertise and professionalism is outstanding.',
            'Truly impressed with how my concerns were addressed.',
            'A standout experience compared to other providers.'
        ];
        
        $neutralComments = [
            'Service was adequate, but there is room for improvement.',
            'The product meets basic needs but could use some enhancements.',
            'Average response time, neither impressive nor disappointing.',
            'Staff was helpful but seemed to lack some technical knowledge.',
            'Website functions well enough but the design is dated.',
            'Reasonable value for money, but I have seen better deals.',
            'Communication was acceptable but could be clearer.',
            'Product quality is consistent but not exceptional.',
            'I had an average experience, nothing remarkable to note.',
            'Response time was standard for the industry.',
            'Some features work well, others need improvement.',
            'The service meets expectations but doesn\'t exceed them.',
            'Would consider using again, but will also look at competitors.',
            'Product does what it says, nothing more, nothing less.',
            'Satisfactory experience overall.',
            'Neither impressed nor disappointed with the service.',
            'Functional but lacks innovation compared to competitors.',
            'It gets the job done, but the process could be smoother.',
            'Somewhat satisfied, though there are areas for improvement.',
            'A standard experience that met basic expectations.'
        ];
        
        $negativeComments = [
            'Response time was too slow for urgent matters.',
            'Staff seemed uninterested in resolving my problem.',
            'Product quality has declined over the past year.',
            'Website is confusing and difficult to navigate.',
            'Multiple attempts needed to resolve a simple issue.',
            'Price is too high for the quality received.',
            'Communication was unclear and caused misunderstandings.',
            'Disappointed with the level of service provided.',
            'Technical issues remain unresolved after several contacts.',
            'Would not recommend based on recent experiences.',
            'Need more training for customer service representatives.',
            'Product did not meet the specifications advertised.',
            'Difficult to find the information I needed on your website.',
            'Long wait times for technical support.',
            'Experience did not meet the expectations set by your advertising.',
            'Customer service representatives were dismissive of my concerns.',
            'The product failed shortly after purchase.',
            'Inconsistent quality between different locations.',
            'Billing issues were not properly addressed.',
            'Found their communication unprofessional and unhelpful.'
        ];
        
        $allComments = array_merge($positiveComments, $neutralComments, $negativeComments);
        
        // Create surveys with questions
        $createdSurveys = [];
        foreach ($surveyTitles as $title) {
            $survey = Survey::create([
                'title' => $title,
                'admin_id' => $admin->id,
                'is_active' => true
            ]);
            
            // Determine if this survey has specialized questions
            $category = $surveyCategories[$title] ?? null;
            
            // Combine common questions with specialized questions if available
            $availableQuestions = $commonQuestions;
            if ($category && isset($specializedQuestions[$category])) {
                $availableQuestions = array_merge($availableQuestions, $specializedQuestions[$category]);
            }
            
            // Get a selection of questions for this survey (5-8 questions per survey)
            $questionCount = rand(5, 8);
            shuffle($availableQuestions);
            $selectedQuestions = array_slice($availableQuestions, 0, $questionCount);
            
            // Add questions to this survey
            foreach ($selectedQuestions as $questionData) {
                $survey->questions()->create([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'required' => $questionData['required']
                ]);
            }
            
            $createdSurveys[] = $survey->load('questions');
        }
        
        // Generate realistic response data
        $startDate = Carbon::now()->subMonths(3); // Responses from the last 3 months
        $endDate = Carbon::now();
        
        // Create a distribution array to generate more realistic ratings
        // This will make the data slightly skewed toward positive ratings
        $ratingDistribution = [
            1 => 5,   // 5% chance of rating 1
            2 => 10,  // 10% chance of rating 2
            3 => 20,  // 20% chance of rating 3
            4 => 35,  // 35% chance of rating 4
            5 => 30   // 30% chance of rating 5
        ];
        
        // Create responses for each survey
        foreach ($createdSurveys as $survey) {
            // Create between 15 and 40 responses for each survey
            $responseCount = rand(15, 40);
            $this->command->info("Creating {$responseCount} responses for survey: {$survey->title}");
            
            for ($i = 0; $i < $responseCount; $i++) {
                // Pick a random account name and type
                $accountName = $companyNames[array_rand($companyNames)];
                $accountType = $accountTypes[array_rand($accountTypes)];
                
                // Generate a random date within the time range
                $responseDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->format('Y-m-d');
                
                // Generate start and end times for the survey (5-15 minutes apart)
                $startTime = Carbon::parse($responseDate . ' ' . rand(8, 17) . ':' . rand(0, 59) . ':' . rand(0, 59));
                $endTime = (clone $startTime)->addMinutes(rand(5, 15));
                
                // Determine recommendation score (1-10)
                // Make the recommendation score correlate somewhat with the average response rating
                $baseRecommendation = rand(1, 10);
                
                // Select a comment based roughly on the recommendation score
                if ($baseRecommendation >= 8) {
                    $comment = $positiveComments[array_rand($positiveComments)];
                } elseif ($baseRecommendation >= 4) {
                    $comment = $neutralComments[array_rand($neutralComments)];
                } else {
                    $comment = $negativeComments[array_rand($negativeComments)];
                }
                
                // Create the response header
                $header = SurveyResponseHeader::create([
                    'survey_id' => $survey->id,
                    'admin_id' => $admin->id,
                    'account_name' => $accountName,
                    'account_type' => $accountType,
                    'date' => $responseDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'recommendation' => $baseRecommendation,
                    'comments' => $comment,
                    'allow_resubmit' => false
                ]);
                
                // Calculate an "opinion factor" based on recommendation
                // This helps correlate ratings across questions for more realistic data
                $opinionFactor = ($baseRecommendation - 5.5) / 4.5; // -1 to 1 scale
                
                // Create responses for each question
                $sumRatings = 0;
                $ratingCount = 0;
                
                foreach ($survey->questions as $question) {
                    if ($question->type === 'radio' || $question->type === 'star') {
                        // Adjust rating probability based on opinion factor
                        $adjustedDistribution = $this->adjustDistribution($ratingDistribution, $opinionFactor);
                        $rating = $this->getRandomRatingFromDistribution($adjustedDistribution);
                        
                        $sumRatings += $rating;
                        $ratingCount++;
                    } else {
                        $rating = "Text response not applicable";
                    }
                    
                    // Create the response detail
                    SurveyResponseDetail::create([
                        'header_id' => $header->id,
                        'question_id' => $question->id,
                        'response' => (string) $rating
                    ]);
                }
                
                // Adjust the recommendation to better correlate with ratings if ratings exist
                if ($ratingCount > 0) {
                    $avgRating = $sumRatings / $ratingCount;
                    $recommendation = min(10, max(1, round(($avgRating * 2) + rand(-2, 2))));
                    $header->recommendation = $recommendation;
                    $header->save();
                }
            }
        }
        
        $this->command->info('Survey test data created successfully!');
    }
    
    /**
     * Adjust rating distribution based on opinion factor.
     * 
     * @param array $distribution
     * @param float $factor Range -1 to 1
     * @return array
     */
    private function adjustDistribution(array $distribution, float $factor): array
    {
        $result = $distribution;
        
        if ($factor > 0) {  // Positive opinion, shift toward higher ratings
            $result[1] = max(1, $distribution[1] - round($factor * 4));
            $result[2] = max(1, $distribution[2] - round($factor * 8));
            $result[3] = $distribution[3] - round($factor * 5);
            $result[4] = $distribution[4] + round($factor * 7);
            $result[5] = $distribution[5] + round($factor * 10);
        } elseif ($factor < 0) {  // Negative opinion, shift toward lower ratings
            $factor = abs($factor);
            $result[1] = $distribution[1] + round($factor * 10);
            $result[2] = $distribution[2] + round($factor * 7);
            $result[3] = $distribution[3] + round($factor * 5);
            $result[4] = max(1, $distribution[4] - round($factor * 8));
            $result[5] = max(1, $distribution[5] - round($factor * 14));
        }
        
        return $result;
    }
    
    /**
     * Get a random rating based on the provided distribution.
     * 
     * @param array $distribution
     * @return int
     */
    private function getRandomRatingFromDistribution(array $distribution): int
    {
        $total = array_sum($distribution);
        $rand = rand(1, $total);
        
        $current = 0;
        foreach ($distribution as $rating => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $rating;
            }
        }
        
        return 3; // Default to middle rating if something goes wrong
    }
}