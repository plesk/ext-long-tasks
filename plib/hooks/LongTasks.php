<?php

class Modules_LongTasksExample_LongTasks extends pm_Hook_LongTasks
{
    public function getLongTasks()
    {
        pm_Log::info('getLongTasks.');
        return [new Modules_LongTasksExample_Task_Succeed(),
            new Modules_LongTasksExample_Task_Fail(),
        ];
    }
}

