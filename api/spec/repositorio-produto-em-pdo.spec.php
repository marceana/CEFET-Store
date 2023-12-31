<?php 
require_once("vendor/autoload.php");
require_once("./src/repositorios/repositorio-produto-em-pdo.php");
require_once("./src/modelos/produto.php");

describe("RepositorioProdutoEmPDO", function(){
    beforeAll(function(){
        $this->pdo = conexaoPDO('cefet-shop');
        $this->repProdPDO = new RepositorioProdutoEmPDO( $this->pdo );
        $this->limite = 10;
        $this->deslocamento = 0;
    });

    describe("obterPagina", function(){
        it("deve retornar um array de produtos", function() {
            $produtos = $this->repProdPDO->obterPagina($this->limite, $this->deslocamento);
             expect( $produtos )->toBeAn('Array');
         });
     
         it("deve retornar um array contendo objetos do tipo Produto", function() {
             $produtos = $this->repProdPDO->obterPagina($this->limite, $this->deslocamento);
             $produto = reset( $produtos );
              expect( $produto )->toBeAnInstanceOf('Produto');
          });
     
          it("deve retornar um array que não esteja vazio", function() {
             $produtos = $this->repProdPDO->obterPagina($this->limite, $this->deslocamento);
              expect( count( $produtos ) )->not->toBe(0);
          });
          it("deve retornar null ao passar limite e deslocamento igual a zero", function() {
             $produtos = $this->repProdPDO->obterPagina(0, 0);
              expect( $produtos  )->toBe(null);
          });
     
          it("deve retornar registros 11 ao 20 quando o limite e deslocamento for igual a 10", function() {
             $produtos = $this->repProdPDO->obterPagina(10,10);
             $primeiroProduto = reset($produtos);
             $ultimoProduto = end($produtos);
              expect( count( $produtos ) )->toBe(10);
              expect($primeiroProduto->getId())->toBe(11);
              expect($ultimoProduto->getId())->toBe(20);
          });
          it("deve checar se todos os atributos necessários estão armazenados nos objetos de Produto paginados", function() {
            //campos: descricao, id, precoDeVenda, imagem, taxaDesconto, total_vendido, precoDesconto
            $produtosPaginados = $this->repProdPDO->obterMaisVendidos();
            foreach($produtosPaginados as $produto){
                $camposNecessariosPresentes = $produto->getDescricao() != null && $produto->getId() > 0 && 
                $produto->getId() != null && $produto->getPrecoDeVenda() != null && $produto->getImagem() != null && 
                $produto->getTotalVendidos() != null && $produto->getTotalVendidos() > 0 && $produto->getPrecoDesconto() >= 0;
                expect($camposNecessariosPresentes)->toBeTruthy();
            }
         });
    });

     describe('obterMaisVendidos', function(){
        it("deve retornar 6 registros do banco de dados", function() {
            $produtosMaisVendidos = $this->repProdPDO->obterMaisVendidos();
            expect(count($produtosMaisVendidos))->toBe(6);
         });
         it("deve garantir que o primeiro registro possui o maior valor no campo totalVendidos", function() {
            $produtosMaisVendidos = $this->repProdPDO->obterMaisVendidos();
            $maisVendido = reset( $produtosMaisVendidos );
            foreach( $produtosMaisVendidos as $p ){
                if($p->getTotalVendidos() > $maisVendido->getTotalVendidos()){
                    $maisVendido = $p;
                }
            }
            expect($maisVendido)->toEqual(reset($produtosMaisVendidos));
         });
         it("deve checar se todos os atributos necessários estão armazenados no objeto Produto", function() {
            //campos: descricao, id, precoDeVenda, imagem, taxaDesconto, total_vendido, precoDesconto
            $produtosMaisVendidos = $this->repProdPDO->obterMaisVendidos();
            foreach($produtosMaisVendidos as $produto){
                $camposNecessariosPresentes = $produto->getDescricao() != null && $produto->getId() > 0 && 
                $produto->getId() != null && $produto->getPrecoDeVenda() != null && $produto->getImagem() != null && 
                $produto->getTotalVendidos() != null && $produto->getTotalVendidos() > 0 && $produto->getPrecoDesconto() >= 0;
                expect($camposNecessariosPresentes)->toBeTruthy();
            }
         });
     });

     describe('obterPorNomeOuId', function(){
        beforeAll(function(){
            $this->produto = new Produto('Boné Turismo', null, null, null, null, null, null, null, null, 1);
        });
        it("deve retornar um objeto produto com todos os atributos do banco de dados", function() {
            $produtoObtido = $this->repProdPDO->obterPorNomeOuId( $this->produto );
            $produtoObtido = end($produtoObtido);
            expect( $produtoObtido )->toBeAnInstanceOf( "Produto" );
            expect( $produtoObtido->getPrecoDeVenda() )->not->toBe(null);
            expect( $produtoObtido->getLancamento() )->not->toBe(null);
            expect( $produtoObtido->getDetalhes() )->not->toBe(null);
            expect( $produtoObtido->getQuantidade() )->not->toBe(null);
            expect( $produtoObtido->getTaxaDesconto() )->not->toBe(null);
            expect( $produtoObtido->getCategoria() )->not->toBe(null);
            expect( $produtoObtido->getImagem() )->not->toBe(null);
            expect( $produtoObtido->getPrecoDesconto() )->not->toBe(null);
         });
         it("deve retornar o registro se apenas o id for informado", function() {
            $this->produto->setDescricao( null );
            $produtoObtido = $this->repProdPDO->obterPorNomeOuId( $this->produto );
            $produtoObtido = end($produtoObtido);
            expect( $produtoObtido->getDescricao() )->toBe('Boné Turismo');
            $this->produto->setDescricao( 'Boné Turismo' );
         });
         xit("deve retornar o registro se apenas o nome/descricao for informado", function() {
            $this->produto->setId( 0 );
            $produtoObtido = $this->repProdPDO->obterPorNomeOuId( $this->produto );
            $produtoObtido = end($produtoObtido);
            expect( $produtoObtido->getId() )->toBe(1);
            $this->produto->setId( 1 );
         });
     });
    
});

?>