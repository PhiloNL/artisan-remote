<?php

Route::get('commands', 'ListCommandsController');
Route::post('commands/invoke', 'InvokeCommandController');
Route::get('environment', 'EnvironmentController');
