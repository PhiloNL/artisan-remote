<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Tests\Stubs;

use Illuminate\Console\Command;

class ExampleCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'example {requiredArgument} {optionalArgument?} {--booleanSwitchOption : Boolean description}
                                 {--optionWithValue= : Option with value description}
                                 {--optionWithArray=* : Option with array description}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example command used for testing...';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->comment('Executing example command...');
            $this->info('Execution completed...');
        } catch (Exception $e) {
            $this->error('Something went wrong');


            return 1;
        }
    }
}