import { VisaoProdutoEmHome } from "./visao-produto-em-home.js";
import { ProdutoRepositorio } from "./produto-repositorio.js";
//import { Util } from "../util/util";

export class ControladoraProdutoEmHome {
  visaoProdutoEmHome: VisaoProdutoEmHome;
  produtoRepositorio: ProdutoRepositorio;

  constructor() {
    this.visaoProdutoEmHome = new VisaoProdutoEmHome();
    this.produtoRepositorio = new ProdutoRepositorio();
  }

  iniciarListagem() {
    this.carregarTodos();
    //Util.aoClicarEmDeslogar(Util.deslogar);
  }

  carregarTodos = async () => {
    try {
      let produtos = await this.produtoRepositorio.obterProdutosMaisVendidos();
      this.visaoProdutoEmHome.montarListagemMaisVendidos(produtos);
    } catch (erro) {
      //Util.mostrarMensagem("Erro ao carregar os produtos. " + erro);
    }
  };
}

const controladoraProdutoEmHome = new ControladoraProdutoEmHome();
controladoraProdutoEmHome.iniciarListagem();