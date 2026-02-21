<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use MelhorEnvio\DTO\CalcularFreteDTO;
use MelhorEnvio\DTO\CriarEnvioDTO;
use MelhorEnvio\Exceptions\ValidationException;
use MelhorEnvio\Models\Address;

class DTOTest extends TestCase
{
    public function test_calcular_frete_dto_validation()
    {
        $dto = new CalcularFreteDTO('', '');
        
        $this->expectException(ValidationException::class);
        $dto->validate();
    }

    public function test_calcular_frete_dto_to_array()
    {
        $dto = new CalcularFreteDTO(
            '29050560',
            '01001000',
            [['weightKg' => 1, 'heightCm' => 10, 'widthCm' => 10, 'lengthCm' => 10]]
        );

        $array = $dto->toArray();

        $this->assertEquals('29050560', $array['from']['postal_code']);
        $this->assertEquals('01001000', $array['to']['postal_code']);
        $this->assertCount(1, $array['packages']);
    }

    public function test_criar_envio_dto_validation()
    {
        $address = new Address('Name', 'Phone', 'Email', 'Doc', 'CEP', 'Street', '10', 'Bairro', 'City', 'ST');
        $dto = new CriarEnvioDTO(1, $address, $address, []);

        $this->expectException(ValidationException::class);
        $dto->validate();
    }
}
