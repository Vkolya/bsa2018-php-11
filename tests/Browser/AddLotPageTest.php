<?php
namespace Tests\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Entity\Currency;

class AddCurrencyPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    public function test_form_is_present()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add')
                    ->assertPresent('form');
            }
        );
    }
    public function test_all_fields_are_empty()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add');
                $this->assertEmpty($browser->value('input[name=currency]'));
                $this->assertEmpty($browser->value('input[name=price]'));
                $this->assertEmpty($browser->value('input[name=date_open]'));
                $this->assertEmpty($browser->value('input[name=date_close]'));
            }
        );
    }
    public function test_validation()
    {
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add');
                $browser->press('Save')
                    ->assertPathIs('/market/lots/add')
                    ->assertSee('The currency field is required.')
                    ->assertSee('The price field is required.')
                    ->assertSee('The date open field is required.')
                    ->assertSee('The date close field is required.');
                $browser
                    ->value('input[name=price]', -2)
                    ->value('input[name=date_open]', 'date')
                    ->value('input[name=date_close]', 'date')
                    ->press('Save')
                    ->assertSee('The price must be at least 0.')
                    ->assertSee('The date open does not match the format d/m/Y H:i.')
                    ->assertSee('The date close does not match the format d/m/Y H:i.');
            }
        );
    }
    public function test_old_values_on_error()
    {
        $this->browse(
            function (Browser $browser) {
                $value = 'test';
                $browser->visit('/market/lots/add')
                    ->value('input[name=currency]', $value)
                    ->value('input[name=price]', $value)
                    ->value('input[name=date_open]', $value)
                    ->value('input[name=date_close]', $value)
                    ->press('Save');
                $this->assertEquals($browser->value('input[name=currency]'), $value);
                $this->assertEquals($browser->value('input[name=price]'), $value);
                $this->assertEquals($browser->value('input[name=date_open]'), $value);
                $this->assertEquals($browser->value('input[name=date_close]'), $value);
            }
        );
    }
    
    public function test_save_currency()
    {
        $currency = factory(Currency::class)->create(['name' => 'Grivna']);
       
        $this->browse(
            function (Browser $browser) {
                $browser->visit('/market/lots/add')
                    ->value('input[name=currency]','Grivna')
                    ->value('input[name=price]', '26')
                    ->value('input[name=date_open]', '13/12/2018 14:22')
                    ->value('input[name=date_close]', '13/12/2018 14:22')
                    ->press('Save')
                    ->assertSee('Lot has been added successfully!');
            }
        );
    }
}