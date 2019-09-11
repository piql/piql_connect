<?php

use App\Bag;
use App\File;
use App\Job;
use Illuminate\Database\Seeder;
use Webpatser\Uuid\Uuid;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $config = [
        'jobs' => [
            [
                'name' => 'Personal records deposit 2017',
                'bags' => [
                    [
                        'name' => 'Personal records deposit first half 2017',
                        'files'=> [
                            [
                                'name' =>'Personal records 2017.03.31',
                                'size' => 30*1024*1024*1024,
                            ],
                            [
                                'name' =>'Personal records 2017.06.30',
                                'size' => 30*1024*1024*1024,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Personal records deposit first second 2017',
                        'files'=> [
                            [
                                'name' => 'Personal records 2017.09.30',
                                'size' => 30*1024*1024*1024,
                            ],
                            [
                                'name' => 'Personal records 2017.12.31',
                                'size' => 30*1024*1024*1024,
                            ]
                        ],
                    ]
                ]
            ],
            [
                'name' => 'Personal records deposit 2018',
                'bags' => [
                    [
                        'name' => 'Personal records deposit first half 2018',
                        'files'=> [
                            [
                                'name' =>'Personal records 2018.03.31',
                                'size' => 30*1024*1024*1024,
                            ],
                            [
                                'name' =>'Personal records 2018.06.30',
                                'size' => 30*1024*1024*1024,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Personal records deposit first second 2018',
                        'files'=> [
                            [
                                'name' => 'Personal records 2018.09.30',
                                'size' => 30*1024*1024*1024,
                            ],
                            [
                                'name' => 'Personal records 2018.12.31',
                                'size' => 30*1024*1024*1024,
                            ]
                        ],
                    ]
                ]
            ],
            [
                'name' => 'Personal records deposit 2019',
                'bags' => [
                    [
                        'name' => 'Personal records deposit first half 2019',
                        'files'=> [
                            [
                                'name' =>'Personal records 2019.03.31',
                                'size' => 30*1024*1024*1024,
                            ],
                            [
                                'name' =>'Personal records 2019.06.30',
                                'size' => 30*1024*1024*1024,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function run()
    {
        $user = \App\User::first();
        if(!isset($user)) {
            error_log("No user found");
            return;
        }

        foreach ($this->config['jobs'] as $jobConfig)
        {
            $job = new Job();
            $job->name = $jobConfig['name'];
            $job->uuid = Uuid::generate();
            $job->owner = $user->id;
            $job->save();

            foreach ($jobConfig['bags'] as $bagConfig) {
                $bag = new Bag();
                $bag->name = $bagConfig['name'];
                $bag->owner = $user->id;
                $bag->save();
                $bag->status = 'complete';
                $bag->save();

                foreach ($bagConfig['files'] as $fileConfig) {
                    $file = new File();
                    $file->fileName = $fileConfig['name'];
                    $file->uuid = Uuid::generate();
                    $file->filesize = $fileConfig['size'];
                    $file->bag_id = $bag->id;
                    $file->save();
                }
                $job->bags()->toggle($bag);
            }
        }
    }
}
