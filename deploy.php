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
    ->set('deploy_path', 'var/www/aydinhassanphotography.com');
