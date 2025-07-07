// Import the necessary classes
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Function to create the tables
function createTables() {
    try {
        // Create survey_improvement_categories table if it doesn't exist
        if (!Schema::hasTable('survey_improvement_categories')) {
            Schema::create('survey_improvement_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
                $table->string('category_name');
                $table->boolean('is_other')->default(false);
                $table->text('other_comments')->nullable();
                $table->timestamps();
            });
            echo "Created survey_improvement_categories table\n";
        } else {
            echo "survey_improvement_categories table already exists\n";
        }

        // Create survey_improvement_details table if it doesn't exist
        if (!Schema::hasTable('survey_improvement_details')) {
            Schema::create('survey_improvement_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('survey_improvement_categories')->onDelete('cascade');
                $table->text('detail_text');
                $table->timestamps();
            });
            echo "Created survey_improvement_details table\n";
        } else {
            echo "survey_improvement_details table already exists\n";
        }

        // Create survey_improvement_areas table if it doesn't exist (for backward compatibility)
        if (!Schema::hasTable('survey_improvement_areas')) {
            Schema::create('survey_improvement_areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('header_id')->constrained('survey_response_headers')->onDelete('cascade');
                $table->string('area_category');
                $table->text('area_details')->nullable();
                $table->boolean('is_other')->default(false);
                $table->text('other_comments')->nullable();
                $table->timestamps();
            });
            echo "Created survey_improvement_areas table\n";
        } else {
            echo "survey_improvement_areas table already exists\n";
        }

        echo "All tables created successfully\n";
        return true;
    } catch (\Exception $e) {
        echo "Error creating tables: " . $e->getMessage() . "\n";
        return false;
    }
}

// Call the function to create tables
createTables();
