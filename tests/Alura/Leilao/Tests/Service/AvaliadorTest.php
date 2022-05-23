<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    /**
     * @var Avaliador
     */
    private $leiloeiro;

    protected function setUp() : void
    {
        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @param Leilao $leilao
     * @return void
     */
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {

        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        self::assertEquals(2500, $maiorValor);
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @param Leilao $leilao
     * @return void
     */
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        self::assertEquals(1700, $menorValor);
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @param Leilao $leilao
     * @return void
     */
    public function testAvaliadorDeveBuscar3MaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiores = $this->leiloeiro->getMaioresLances();
        static::assertCount(3, $maiores);
        static::assertEquals(2500, $maiores[0]->getValor());
        static::assertEquals(2000, $maiores[1]->getValor());
        static::assertEquals(1700, $maiores[2]->getValor());
    }

    public function testLeilaoVazioNaoPodeSerAvaliado()
    {

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não foi possível avaliar leilões vazio');

        $leilao = new Leilao('Fusca Azul');
        $this->leiloeiro->avalia($leilao);

    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliador()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');

        $leilao = new Leilao('Fiat 147 0km');
        $leilao->recebeLance(new Lance(new Usuario('Teste'), 2000));
        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);

    }
    
    public function leilaoEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 OK');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));

        return [
            'Ordem-crescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 OK');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 1700));

        return [
            'ordem-decrescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemAleatoria()
    {
        $leilao = new Leilao('Fiat 147 OK');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($maria, 2500));

        return [
            'ordem-aleatoria' => [$leilao]
        ];
    }

}