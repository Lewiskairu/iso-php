SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'key, value FROM site_settings WHERE key IN ('hero_title', 'hero_subtitle', 'h...' at line 1
#0 /opt/lampp/htdocs/php-system-ready/app/app/Core/Database.php(60): PDOStatement->execute(Array)
#1 /opt/lampp/htdocs/php-system-ready/app/app/Repositories/ContentRepository.php(128): App\Core\Database::query('SELECT key, val...', Array)
#2 /opt/lampp/htdocs/php-system-ready/app/app/Controllers/HomeController.php(20): App\Repositories\ContentRepository->heroSettings()
#3 /opt/lampp/htdocs/php-system-ready/app/app/Core/Router.php(46): App\Controllers\HomeController->index()
#4 /opt/lampp/htdocs/php-system-ready/app/app/Core/App.php(30): App\Core\Router->dispatch('GET', '/')
#5 /opt/lampp/htdocs/php-system-ready/app/public/index.php(22): App\Core\App->run()
#6 /opt/lampp/htdocs/php-system-ready/index.php(5): require('/opt/lampp/htdo...')
#7 {main}