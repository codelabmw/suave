<?php

it('can run suave install command', function () {
    $this->artisan('suave:install')
        ->expectsQuestion('Do you want to continue?', 'yes')
        ->assertExitCode(0);
});
