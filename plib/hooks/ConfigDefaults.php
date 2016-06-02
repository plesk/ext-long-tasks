<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_LongTasksExample_ConfigDefaults extends pm_Hook_ConfigDefaults
{
    public function getDefaults()
    {
        return [
            'duration' => 15,
            'allowRedirectToDomain' => true,
        ];
    }
}