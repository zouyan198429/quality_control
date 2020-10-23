<?php

namespace App\Console;

use App\Business\API\RunBuy\CityAPIBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsResultsDBBusiness;
use App\Business\DB\QualityControl\AbilitysDBBusiness;
use App\Business\DB\QualityControl\CompanyGradeConfigDBBusiness;
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
            // CityAPIBusiness::autoCancelOrdes();// 跑城市订单过期未接单自动关闭脚本--每一分钟跑一次
            AbilitysDBBusiness::autoBeginJoin();// 未开始的，时间一到进入到开始报名
            AbilitysDBBusiness::autoBeginDoing();// 开始报名的，时间一到结束，进入到进行中
            AbilitysDBBusiness::autoPublishDoing();// 指定时间公布的，时间一到结束，进行公布--每一分钟跑一次
            // AbilityJoinItemsResultsDBBusiness::autosSubmitOverTime();// 如果企业没有按时提交数据，则自动判定结果为不满意--上传数据超时
            CompanyGradeConfigDBBusiness::autoGradeConfig();// 对到时间的会员等级进行处理
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
