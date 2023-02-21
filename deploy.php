<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'aydinhassanphotography');

// Project repository
set('repository', 'git@github.com:AydinHassan/aydinhassanphotography.com.git');

set('git_tty', true);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('aydin@personal')
    ->set('deploy_path', '/var/www/aydinhassanphotography.com');

after('deploy:failed', 'deploy:unlock');

task('deploy:vendors', function () {
    run('cd {{release_path}} && npm i');
});

task('deploy:build', function () {
    run('cd {{release_path}} && npm run build');
});

/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:build',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');