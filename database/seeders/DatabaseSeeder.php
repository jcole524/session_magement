<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Exercise;
use App\Models\Goal;
use App\Models\ProgressLog;
use App\Models\SessionExercise;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@ptssms.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        // ── 10 Sample Users ───────────────────────────────────────────────────
        $users = [];
        $names = [
            ['Juan dela Cruz',    'juan@example.com',    'male',   '1995-04-15', '09171234567'],
            ['Maria Santos',      'maria@example.com',   'female', '1998-06-22', '09281234567'],
            ['Carlo Reyes',       'carlo@example.com',   'male',   '1993-11-08', '09351234567'],
            ['Ana Lim',           'ana@example.com',     'female', '2000-03-14', '09461234567'],
            ['Marcos Villanueva', 'marcos@example.com',  'male',   '1990-09-30', '09571234567'],
            ['Sofia Garcia',      'sofia@example.com',   'female', '1997-01-25', '09681234567'],
            ['Paolo Mendoza',     'paolo@example.com',   'male',   '1994-07-18', '09791234567'],
            ['Lea Torres',        'lea@example.com',     'female', '1999-12-05', '09801234567'],
            ['Kevin Cruz',        'kevin@example.com',   'male',   '1996-05-11', '09911234567'],
            ['Nina Ramos',        'nina@example.com',    'female', '2001-08-27', '09021234567'],
        ];

        foreach ($names as $n) {
            $users[] = User::create([
                'name'          => $n[0],
                'email'         => $n[1],
                'password'      => Hash::make('password'),
                'role'          => 'user',
                'status'        => 'active',
                'gender'        => $n[2],
                'date_of_birth' => $n[3],
                'phone'         => $n[4],
            ]);
        }

        // ── Exercise Library ──────────────────────────────────────────────────
        $exercises = [
            ['Bench Press',        'Strength',    'Chest'],
            ['Squat',              'Strength',    'Legs'],
            ['Deadlift',           'Strength',    'Back'],
            ['Pull-up',            'Strength',    'Back'],
            ['Overhead Press',     'Strength',    'Shoulders'],
            ['Barbell Row',        'Strength',    'Back'],
            ['Dumbbell Curl',      'Strength',    'Biceps'],
            ['Tricep Pushdown',    'Strength',    'Triceps'],
            ['Leg Press',          'Strength',    'Legs'],
            ['Lunges',             'Strength',    'Legs'],
            ['Incline Press',      'Strength',    'Chest'],
            ['Cable Fly',          'Strength',    'Chest'],
            ['Face Pull',          'Strength',    'Shoulders'],
            ['Lateral Raise',      'Strength',    'Shoulders'],
            ['Hammer Curl',        'Strength',    'Biceps'],
            ['Treadmill Run',      'Cardio',      'Full Body'],
            ['Cycling',            'Cardio',      'Legs'],
            ['Jump Rope',          'Cardio',      'Full Body'],
            ['Rowing Machine',     'Cardio',      'Full Body'],
            ['Stair Climber',      'Cardio',      'Legs'],
            ['Plank',              'Core',        'Core'],
            ['Crunches',           'Core',        'Abs'],
            ['Russian Twist',      'Core',        'Obliques'],
            ['Leg Raise',          'Core',        'Abs'],
            ['Mountain Climbers',  'Core',        'Core'],
            ['Yoga Flow',          'Flexibility', 'Full Body'],
            ['Hip Flexor Stretch', 'Flexibility', 'Hips'],
            ['Hamstring Stretch',  'Flexibility', 'Legs'],
            ['Shoulder Stretch',   'Flexibility', 'Shoulders'],
            ['Foam Rolling',       'Flexibility', 'Full Body'],
        ];

        $exIds = [];
        foreach ($exercises as $ex) {
            $exIds[] = Exercise::firstOrCreate(
                ['name' => $ex[0]],
                [
                    'category'     => $ex[1],
                    'muscle_group' => $ex[2],
                    'status'       => 'active',
                ]
            )->id;
        }

        // ── Sessions + Exercises + Progress + Goals per user ──────────────────
        $sessionTitles = [
            'Morning Push Day', 'Leg Day Grind', 'Pull Day', 'Cardio Blast',
            'Core & Abs', 'Upper Body', 'Lower Body', 'Full Body Burn',
            'Active Recovery', 'Strength Training', 'HIIT Session', 'Flexibility Day',
            'Back & Biceps', 'Chest & Triceps', 'Shoulders & Core',
        ];

        $goalTypes = ['Weight Loss', 'Muscle Gain', 'Endurance', 'Flexibility', 'Consistency', 'Strength'];
        $goalDescs = [
            'Reach target body weight within 3 months.',
            'Increase bench press to 100 kg.',
            'Run 5km without stopping.',
            'Touch toes without bending knees.',
            'Work out at least 4 times per week.',
            'Deadlift 1.5x body weight.',
            'Complete 10 pull-ups in a row.',
            'Lose 5 kg in 2 months.',
            'Build visible abs by end of quarter.',
            'Improve sprint speed by 10%.',
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < 15; $i++) {
                $daysAgo = rand(-5, 30);
                $status  = $daysAgo > 2 ? 'completed' : ($daysAgo < 0 ? 'scheduled' : 'active');
                $startH  = rand(5, 18);
                $endH    = $startH + rand(1, 2);

                $session = WorkoutSession::create([
                    'user_id'      => $user->id,
                    'title'        => $sessionTitles[array_rand($sessionTitles)],
                    'session_date' => now()->subDays($daysAgo)->toDateString(),
                    'start_time'   => sprintf('%02d:00', $startH),
                    'end_time'     => sprintf('%02d:00', $endH),
                    'status'       => $status,
                    'notes'        => rand(0, 1) ? 'Felt great today. Hit a new PR!' : null,
                ]);

                $count  = rand(3, 5);
                $picked = array_rand(array_flip($exIds), $count);
                foreach ((array) $picked as $exId) {
                    SessionExercise::create([
                        'session_id'    => $session->id,
                        'exercise_id'   => $exId,
                        'sets'          => rand(3, 5),
                        'reps'          => rand(6, 15),
                        'weight_kg'     => rand(20, 120),
                        'duration_mins' => rand(0, 1) ? rand(10, 45) : null,
                    ]);
                }

                if ($status === 'completed') {
                    ProgressLog::create([
                        'user_id'        => $user->id,
                        'session_id'     => $session->id,
                        'body_weight_kg' => round(rand(55, 95) + rand(0, 9) / 10, 1),
                        'log_date'       => now()->subDays($daysAgo)->toDateString(),
                        'notes'          => rand(0, 1) ? 'Feeling strong. Recovery good.' : null,
                    ]);
                }
            }

            $goalCount = rand(4, 6);
            for ($g = 0; $g < $goalCount; $g++) {
                $statuses = ['active', 'active', 'active', 'achieved', 'cancelled'];
                Goal::create([
                    'user_id'     => $user->id,
                    'type'        => $goalTypes[array_rand($goalTypes)],
                    'description' => $goalDescs[array_rand($goalDescs)],
                    'target_date' => now()->addDays(rand(15, 180))->toDateString(),
                    'status'      => $statuses[array_rand($statuses)],
                ]);
            }
        }

        // ── Solo Leveling Challenges ──────────────────────────────────────────
        $admin      = User::where('role', 'admin')->first();
        $benchPress = Exercise::where('name', 'Bench Press')->first();
        $squat      = Exercise::where('name', 'Squat')->first();

        $slChallenges = [
            [
                'title'       => 'Sung Jin-Woo Daily Quest',
                'description' => 'The System has issued a daily quest. Complete 12 sessions this month — roughly 3 times per week. Rise from E-Rank to S-Rank or face the penalty.',
                'type'        => 'session_count',
                'target'      => 12,
                'exercise_id' => null,
                'start_date'  => now()->startOfMonth()->toDateString(),
                'end_date'    => now()->endOfMonth()->toDateString(),
            ],
            [
                'title'       => 'Igris the Shadow Knight',
                'description' => 'To extract Igris as a shadow soldier, prove your chest strength. Complete 40 bench press reps across your sessions within 30 days.',
                'type'        => 'specific_exercise',
                'target'      => 40,
                'exercise_id' => $benchPress?->id,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(30)->toDateString(),
            ],
            [
                'title'       => 'The Penalty Zone',
                'description' => 'Failure is not an option. Train 5 consecutive days without missing a single one or face the Penalty Zone.',
                'type'        => 'streak',
                'target'      => 5,
                'exercise_id' => null,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(14)->toDateString(),
            ],
            [
                'title'       => 'Arise — S-Rank Strength',
                'description' => 'Only those who reach S-Rank deserve to command shadows. Lift a total of 3,000 kg across all your sessions within 60 days to prove you are worthy.',
                'type'        => 'total_weight',
                'target'      => 3000,
                'exercise_id' => null,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(60)->toDateString(),
            ],
            [
                'title'       => 'Beru\'s 100 Rep Mandate',
                'description' => 'The Ant King demands results. Complete 40 bench press reps across your sessions within 30 days to earn Beru\'s respect.',
                'type'        => 'specific_exercise',
                'target'      => 40,
                'exercise_id' => $benchPress?->id,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(30)->toDateString(),
            ],
            [
                'title'       => 'Double Dungeon Survivor',
                'description' => 'Survive the Double Dungeon before the gate closes. Complete 8 sessions within 30 days. No retreating.',
                'type'        => 'session_count',
                'target'      => 8,
                'exercise_id' => null,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(30)->toDateString(),
            ],
            [
                'title'       => 'Monarch of Shadows',
                'description' => 'Only the strongest earn the title of Monarch. Train 7 consecutive days without breaking the chain to claim your throne.',
                'type'        => 'streak',
                'target'      => 7,
                'exercise_id' => null,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(21)->toDateString(),
            ],
            [
                'title'       => 'Goto Ryuji\'s Squat War',
                'description' => 'The S-Rank hunter Goto challenges your leg strength. Complete 60 squat reps across your sessions within 30 days to defeat him.',
                'type'        => 'specific_exercise',
                'target'      => 60,
                'exercise_id' => $squat?->id,
                'start_date'  => now()->toDateString(),
                'end_date'    => now()->addDays(30)->toDateString(),
            ],
        ];

        foreach ($slChallenges as $c) {
            Challenge::create(array_merge($c, [
                'created_by' => $admin->id,
                'status'     => 'open',
            ]));
        }
    }
}