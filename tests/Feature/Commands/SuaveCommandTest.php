<?php

it('can run suave install command', function () {
    $this->artisan('suave:install')
        ->expectsChoice('What would you like to install?', ['Tokens', 'Sessions'], ['Tokens', 'Sessions'])
        ->expectsQuestion('Do you want to continue?', 'yes')
        ->assertExitCode(0);
});

it('can get consent from user before continuing', function () {
    $this->artisan('suave:install')
        ->expectsChoice('What would you like to install?', ['Tokens', 'Sessions'], ['Tokens', 'Sessions'])
        ->expectsQuestion('Do you want to continue?', 'yes')
        ->assertExitCode(0);
});
