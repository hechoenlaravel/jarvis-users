<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JarvisUsersModuleAuthTest extends TestCase
{

    use DatabaseMigrations, DatabaseTransactions;

    /**
     * @test
     */
    public function it_logs_user_in()
    {
        $this->installApp();
        $this->visit('auth/login')
            ->type('admin@admin.com', 'email')
            ->type('admin', 'password')
            ->press('Ingresar')
            ->seePageIs('/dashboard/demo');
    }

}