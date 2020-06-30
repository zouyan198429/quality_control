<?php

namespace App\Console;

use App\Business\API\RunBuy\CityAPIBusiness;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            CityAPIBusiness::autoCancelOrdes();// 跑城市订单过期未接单自动关闭脚本--每一分钟跑一次
        })->everyMinute();// 每分钟执行一次 锁会在 5 分钟后失效->withoutOverlapping(5)[会失败] ;  ->appendOutputTo($filePath)

        // Horizon 包含一个 Metrics 仪表盘，它可以提供任务和队列等待时间和吞吐量信息，
        // 为了填充此仪表盘，你需要配置应用的 snapshot 每五分钟运行一次 Horizon 的 scheduler 每五分钟运行一次 Horizon 的
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
