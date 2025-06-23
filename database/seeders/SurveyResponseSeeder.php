<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SurveyResponseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get all active surveys and users
        $surveys = Survey::where('is_active', true)->with('questions')->get();
        $users = User::where('status', 1)->get();
        
        if ($surveys->isEmpty() || $users->isEmpty()) {
            $this->command->error('Please ensure active surveys and users exist before running this seeder');
            return;
        }
        
        $this->command->info('Creating survey responses...');
        
        foreach ($surveys as $survey) {
            // Create 10-30 responses per survey
            $responseCount = $faker->numberBetween(10, 30);
            
            for ($i = 0; $i < $responseCount; $i++) {
                $this->createSurveyResponse($faker, $survey, $users);
            }
        }
        
        $this->command->info('Survey response seeding completed!');
    }    private function createSurveyResponse($faker, $survey, $users)
    {
        $admins = \App\Models\Admin::where('status', 1)->get();
        
        // Create response header
        $responseHeader = SurveyResponseHeader::create([
            'survey_id' => $survey->id,
            'admin_id' => $admins->isNotEmpty() ? $admins->random()->id : null,
            'account_name' => $faker->name,
            'account_type' => $faker->randomElement(['Customer', 'Employee', 'Partner', 'Visitor']),
            'date' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'recommendation' => $faker->numberBetween(1, 5),
            'comments' => $faker->optional(0.7)->paragraph(),
        ]);
          // Create response details for each question
        foreach ($survey->questions as $question) {
            $responseValue = $this->generateResponseValue($faker, $question);
            
            SurveyResponseDetail::create([
                'header_id' => $responseHeader->id,
                'question_id' => $question->id,
                'response' => $responseValue,
            ]);
        }
    }
    
    private function generateResponseValue($faker, $question)
    {
        switch ($question->type) {
            case 'text':
                return $this->generateTextResponse($faker, $question->text);
                
            case 'textarea':
                return $this->generateTextareaResponse($faker, $question->text);
                
            case 'radio':
                return $this->generateRadioResponse($faker, $question->text);
                
            case 'checkbox':
                return $this->generateCheckboxResponse($faker, $question->text);
                
            case 'select':
                return $this->generateSelectResponse($faker, $question->text);
                
            case 'number':
                return $faker->numberBetween(1, 100);
                
            case 'email':
                return $faker->email;
                
            case 'date':
                return $faker->date();
                
            default:
                return $faker->word;
        }
    }
    
    private function generateTextResponse($faker, $questionText)
    {
        if (stripos($questionText, 'name') !== false) {
            return $faker->name;
        } elseif (stripos($questionText, 'position') !== false || stripos($questionText, 'role') !== false) {
            return $faker->randomElement([
                'Customer Service Representative', 'Sales Associate', 'Team Lead',
                'Supervisor', 'Manager', 'Assistant Manager', 'Analyst',
                'Coordinator', 'Specialist', 'Officer'
            ]);
        } elseif (stripos($questionText, 'department') !== false || stripos($questionText, 'area') !== false) {
            return $faker->randomElement([
                'Customer Service', 'Sales', 'Operations', 'HR', 'Finance',
                'IT', 'Marketing', 'Quality Assurance', 'Training'
            ]);
        } elseif (stripos($questionText, 'employee id') !== false || stripos($questionText, 'id') !== false) {
            return $faker->numerify('EMP####');
        } else {
            return $faker->sentence(3);
        }
    }
    
    private function generateTextareaResponse($faker, $questionText)
    {
        if (stripos($questionText, 'accomplishment') !== false) {
            $accomplishments = [
                'Successfully exceeded quarterly sales targets by 15% through improved customer relationship management.',
                'Led a cross-functional team project that reduced processing time by 30%.',
                'Implemented a new customer feedback system that improved satisfaction scores.',
                'Completed advanced training certifications and mentored 3 new team members.',
                'Streamlined workflow processes resulting in 20% efficiency improvement.'
            ];
            return $faker->randomElement($accomplishments);
        } elseif (stripos($questionText, 'challenge') !== false) {
            $challenges = [
                'Adapting to new software systems required additional training and practice.',
                'Managing increased workload during peak seasons while maintaining quality standards.',
                'Coordinating with remote team members across different time zones.',
                'Handling difficult customer situations while maintaining professionalism.',
                'Balancing multiple priority projects with competing deadlines.'
            ];
            return $faker->randomElement($challenges);
        } elseif (stripos($questionText, 'improvement') !== false || stripos($questionText, 'suggest') !== false) {
            $improvements = [
                'Better communication tools for team collaboration.',
                'More flexible work arrangements and scheduling options.',
                'Additional training programs for skill development.',
                'Improved workspace ergonomics and equipment.',
                'Regular team building activities and recognition programs.'
            ];
            return $faker->randomElement($improvements);
        } elseif (stripos($questionText, 'goal') !== false) {
            $goals = [
                'Improve technical skills through advanced training and certifications.',
                'Take on more leadership responsibilities and mentor junior staff.',
                'Increase customer satisfaction scores by implementing best practices.',
                'Develop better time management and organizational skills.',
                'Contribute to process improvement initiatives.'
            ];
            return $faker->randomElement($goals);
        } else {
            return $faker->paragraph();
        }
    }
    
    private function generateRadioResponse($faker, $questionText)
    {
        if (stripos($questionText, 'satisfied') !== false || stripos($questionText, 'rate') !== false) {
            return $faker->randomElement(['Very Satisfied', 'Satisfied', 'Neutral', 'Dissatisfied', 'Very Dissatisfied']);
        } elseif (stripos($questionText, 'likely') !== false && stripos($questionText, 'recommend') !== false) {
            return $faker->randomElement(['Very Likely', 'Likely', 'Neutral', 'Unlikely', 'Very Unlikely']);
        } elseif (stripos($questionText, 'agree') !== false) {
            return $faker->randomElement(['Strongly Agree', 'Agree', 'Neutral', 'Disagree', 'Strongly Disagree']);
        } elseif (stripos($questionText, 'quality') !== false) {
            return $faker->randomElement(['Excellent', 'Good', 'Average', 'Poor', 'Very Poor']);
        } elseif (stripos($questionText, 'frequency') !== false || stripos($questionText, 'often') !== false) {
            return $faker->randomElement(['Daily', 'Weekly', 'Monthly', 'Rarely', 'Never']);
        } elseif (stripos($questionText, 'confident') !== false) {
            return $faker->randomElement(['Very Confident', 'Confident', 'Somewhat Confident', 'Not Confident']);
        } else {
            return $faker->randomElement(['Yes', 'No', 'Maybe', 'Not Sure']);
        }
    }
    
    private function generateCheckboxResponse($faker, $questionText)
    {
        if (stripos($questionText, 'improve') !== false) {
            $options = ['Communication', 'Training', 'Equipment', 'Processes', 'Management', 'Benefits'];
            return implode(', ', $faker->randomElements($options, $faker->numberBetween(1, 3)));
        } elseif (stripos($questionText, 'service') !== false) {
            $options = ['Customer Support', 'Technical Support', 'Sales', 'Billing', 'Training', 'Consulting'];
            return implode(', ', $faker->randomElements($options, $faker->numberBetween(1, 3)));
        } elseif (stripos($questionText, 'training') !== false) {
            $options = ['Classroom Training', 'Online Learning', 'Hands-on Practice', 'Mentoring', 'Group Discussions'];
            return implode(', ', $faker->randomElements($options, $faker->numberBetween(1, 3)));
        } elseif (stripos($questionText, 'safety') !== false) {
            $options = ['Equipment Safety', 'Fire Safety', 'Emergency Procedures', 'Chemical Safety', 'Ergonomics'];
            return implode(', ', $faker->randomElements($options, $faker->numberBetween(1, 3)));
        } else {
            $options = ['Option A', 'Option B', 'Option C', 'Option D', 'Option E'];
            return implode(', ', $faker->randomElements($options, $faker->numberBetween(1, 3)));
        }
    }
    
    private function generateSelectResponse($faker, $questionText)
    {
        if (stripos($questionText, 'long') !== false && stripos($questionText, 'company') !== false) {
            return $faker->randomElement(['Less than 1 year', '1-2 years', '3-5 years', '6-10 years', 'More than 10 years']);
        } elseif (stripos($questionText, 'training') !== false && stripos($questionText, 'program') !== false) {
            return $faker->randomElement(['Orientation Training', 'Technical Skills', 'Leadership Development', 'Safety Training', 'Customer Service']);
        } elseif (stripos($questionText, 'learn') !== false && stripos($questionText, 'service') !== false) {
            return $faker->randomElement(['Search Engine', 'Social Media', 'Word of Mouth', 'Advertisement', 'Referral']);
        } elseif (stripos($questionText, 'safety') !== false && stripos($questionText, 'training') !== false) {
            return $faker->randomElement(['Monthly', 'Quarterly', 'Semi-annually', 'Annually', 'As needed']);
        } else {
            return $faker->randomElement(['Option 1', 'Option 2', 'Option 3', 'Option 4', 'Option 5']);
        }
    }
}
