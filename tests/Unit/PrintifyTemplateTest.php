<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\PrintSuppliers\Controllers\PrintifyTemplate;

class PrintifyTemplateTest extends TestCase
{
    /**
     * @test
     * @dataProvider provider
     */
    public function get_template($input, $output)
    {
        $printifyTemplate = new PrintifyTemplate;
        $response = $printifyTemplate->getTemplate($input);
        if ($response) {
            $this->assertSame($output, $response);
        }

    }

    public function provider () {
        return [
            [["7", "Black", "M", "Men", "", 'front'], [6,29,12125, "3909x4430"]],
            [["7", "Black", "M", "Men", "Classic T-shirt", 'front'], [36,45,22088, "3129x4173"]],
        ];
    }
}
