<?php    
    error_reporting(0);
    header('Content-Type: text/html; charset=utf-8');

    include_once('./classes/Functions.class.php');

    $url = "http://".$_SERVER['HTTP_HOST'].str_replace("index.php", "request.php",$_SERVER['PHP_SELF']);
    $json = file_get_contents($url);
    // echo $json;
    // exit;
    $fields = json_decode($json, true);


    $functions = new Functions();

    // $defaults = [
    //     "type"      => @$_GET["tipo"] ? @$_GET["tipo"] : "day",
    //     "month"     => (@$_GET["mes"] && !$functions->isFutureDate(@$_GET["mes"])) ? @$_GET["mes"] : date('Y-m'),
    //     "year"      => (@$_GET["ano"] && !$functions->isFutureDate(@$_GET["ano"])) ? @$_GET["ano"] : date('Y'),
    //     "consulta"  => @$_GET["consulta"] ? @$_GET["consulta"] : "",
    //     "provedor"  => @$_GET["provedor"] ? @$_GET["provedor"] : "",
    //     "uf"        => @$_GET["uf"] ? @$_GET["uf"] : "",
    //     "cliente"        => @$_GET["cliente"] ? @$_GET["cliente"] : "",
    // ];

    // $textConsulta = "";
    // foreach ((array)$fields["consulta"] as $item) {
    //     if ($item["tipoConsulta"] === $defaults["consulta"]) {
    //         $textConsulta = $item["nomeConsulta"];
    //     }
    // }


    function build_http_query( $query ) {
        $query_array = array();
        $default_query = array_merge((array)$_GET, $query);
        foreach( $default_query as $key => $key_value ){
            $query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );
    
        }
        return implode( '&', $query_array );
    }

    function on_previous_page ($key_param, $query=array()) {
        $value = (int)@$_GET[$key_param];
        if ($value > 0) $value -= 1;
        return build_http_query(array_merge(array( $key_param => $value ), $query));
    }


    function on_next_page ($key_param, $query=array()) {
        $value = (int)@$_GET[$key_param];
        if ($value < 999) $value += 1;
        return build_http_query(array_merge(array( $key_param => $value ), $query));
    }


    $representantes_total = 20;
    $representantes_paginas = 8;
    $representantes = array();
    $representantes[] = array(
        "id" => 1,
        "representante" => "Joao Melo",
        "status" => 0,
    );
    $representantes[] = array(
        "id" => 2,
        "representante" => "Paulinho",
        "status" => 0,
    );
    $representantes[] = array(
        "id" => 3,
        "representante" => "Milton",
        "status" => 0,
    );

    


    // vendPage = 2
    // regiPage = 2
    // regiEdit
    // vendId
    // vendStatus = 1
    // regiStatus

?>

    <?php include_once("templates/header.php"); ?>

    <style>

        #loading-container {
            z-index: 999 !important;
            opacity: .75;
        }

        .progress-bar {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            background-color: #007bff;
            transition: width .1s ease;
        }
        .progress {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            height: 1rem;
            overflow: hidden;
            font-size: .75rem;
            background-color: #e9ecef;
            border-radius: .25rem;
        }

        .bd-search::after {
            position: absolute;
            top: .4rem;
            right: .4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 1.5rem;
            padding-right: .25rem;
            padding-left: .25rem;
            font-size: .75rem;
            color: #6c757d;
            content: "Ctrl + /";
            border: 1px solid #dee2e6;
            border-radius: .125rem;
        }
    </style>


    <div id="loading-container" style="display: none !important; z-index: 999 !important;" class="bg-white position-absolute d-flex w-100 h-100 m-0 p-0 flex-column align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
        </div>
        <div class="progress mt-2" style="width: 200px;">
            <div id="loading-progress" class="progress-bar" role="progressbar" style="width: 0% !important;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>


    <div class="row h-100">
        <div class="col-3 h-100">

        <div class="p-4 px-0">
   
            <div class="input-group">
                <button type="button" class="btn btn-primary py-2 px-3 text-start" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <span class="mdi mdi-plus-circle-outline fs-4"></span> 
                    <span class="pe-2" style="vertical-align: text-bottom;">Criar Registro</span>
                </button>
                <!-- <button type="button" class="btn px-3 btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button> -->
                <!-- <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                </ul> -->
            </div>
        </div>

            <nav class="navbar navbar-expand w-100 mb-2">
                <ul class="navbar-nav w-100 text-center p-1 bg-white border rounded">
                    <li class="nav-item w-100 border-end">
                        <a class="nav-link <?php echo ($_GET["vend_status"] == "todos" || !@$_GET["vend_status"]) ? "active" : ""; ?>" 
                            aria-current="page" 
                            href="?<?php echo build_http_query(array( "vend_status" => "todos")); ?>"
                        >Todos</a>
                    </li>
                    <li class="nav-item w-100 border-end">
                        <a class="nav-link <?php echo $_GET["vend_status"] == "ativos" ? "active" : ""; ?>" 
                            aria-current="page" 
                            href="?<?php echo build_http_query(array( "vend_status" => "ativos")); ?>"
                        >Ativos</a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link <?php echo $_GET["vend_status"] == "inativos" ? "active" : ""; ?>" 
                            href="?<?php echo build_http_query(array( "vend_status" => "inativos")); ?>"
                        >Inativos</a>
                    </li>
                </ul>
            </nav>

            <form class="bd-search position-relative mb-2">
                <div 
                    class="algolia-autocomplete " 
                    style="position: relative;"
                >
                <div class="input-group align-items-center mb-3">
                    <button style="min-width: 32px; z-index: 99;" class="btn btn-outline-secondary border-0" type="submit" id="button-addon1">
                        <span class="mdi mdi-magnify"></span>
                    </button>
                    <input type="search" 
                        name="vend_name"
                        class="form-control ds-input rounded w-100" 
                        id="search-input" 
                        placeholder="Buscar por nome..." 
                        aria-label="Buscar por nome..." 
                        autocomplete="off" 
                        data-bd-docs-version="5.0" 
                        spellcheck="false" role="combobox" 
                        aria-autocomplete="list" 
                        aria-expanded="false" 
                        aria-owns="algolia-autocomplete-listbox-0" 
                        dir="auto" 
                        style="position: absolute;  padding-left: 42px;"
                    >
                </div>
                   
                </div>
            </form>
            
            <nav class="navbar bg-white border rounded navbar-expand-lg bg-light">
                <div class="h-100 p-2">
                    <div class="w-100 d-flex flex-row align-items-center justify-content-between">
                        <small class="mb-0 p-2 px-3 text-muted">1-5 de 50</small>
                        <div>
                            <a  
                                type="button" 
                                class="btn btn-link"
                                href="?<?php echo on_previous_page("vend_page", array( "regi_page" => 0 )); ?>"
                            >
                                <span class="mdi mdi-chevron-left"></span>
                            </a>
                            <a 
                                type="button" 
                                class="btn btn-link"
                                href="?<?php echo on_next_page("vend_page", array( "regi_page" => 0 )); ?>"
                            >
                                <span class="mdi mdi-chevron-right"></span>
                            </a>
                        </div>
                    </div>
                    <ul class="navbar-nav p-2 row">
                        <?php 
                            foreach ($representantes as $index => $representante) { ?>
                                <li class="nav-item col-12">
                                    <a class="nav-link <?php 
                                        $class = "";
                                        if (@$_GET["vend_id"] == $representante["id"]) {
                                            $class = "active";
                                        } 
                                        if (!@$_GET["vend_id"] && $index === 0) {
                                            $class = "active";
                                        }
                                        echo $class; 
                                    ?>" 
                                    aria-current="page" 
                                    href="?<?php echo build_http_query(array( "vend_id" => $representante["id"])); ?>"
                                >
                                        <?php echo $representante["representante"]; ?>
                                    </a>
                                </li>
                            <?php }
                        ?>
                    </ul>
                </div>
                </nav>
            </div>
            <div class="col-9 py-4 h-100">
                <div class="container bg-white border rounded h-100">
                <div class="h-100">

                    <div class="row">
                        <div class="col">
                            <div class="d-flex">

           

                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-row align-items-center justify-content-end p-3">
                                <small class="mb-0 text-muted">1-50 de 18.600</small>
                                <a 
                                    type="button" 
                                    class="btn btn-link"
                                    href="?<?php echo on_previous_page("regi_page"); ?>"
                                >
                                    <span class="mdi mdi-chevron-left"></span>
                                </a>
                                <a 
                                    type="button" 
                                    class="btn btn-link"
                                    href="?<?php echo on_next_page("regi_page"); ?>"
                                >
                                    <span class="mdi mdi-chevron-right"></span>
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="h-100 d-flex align-items-center justify-content-between">
                                <nav class="navbar navbar-expand px-4">
                                    <ul class="navbar-nav w-100 text-center p-0 bg-white border rounded">
                                        <li class="nav-item w-100 border-end">
                                            <a class="p-1 px-3 nav-link <?php echo ($_GET["regi_status"] == "todos" || !@$_GET["regi_status"]) ? "active" : ""; ?>" 
                                                aria-current="page" 
                                                href="?<?php echo build_http_query(array( "regi_status" => "todos")); ?>"
                                            >Todos</a>
                                        </li>
                                        <li class="nav-item w-100 border-end">
                                            <a class="p-1 px-3 nav-link <?php echo $_GET["regi_status"] == "pendentes" ? "active" : ""; ?>" 
                                                aria-current="page" 
                                                href="?<?php echo build_http_query(array( "regi_status" => "pendentes")); ?>"
                                            >Pendentes</a>
                                        </li>
                                        <li class="nav-item w-100">
                                            <a class="p-1 px-3 nav-link <?php echo $_GET["regi_status"] == "pagos" ? "active" : ""; ?>" 
                                                href="?<?php echo build_http_query(array( "regi_status" => "pagos")); ?>"
                                            >Pagos</a>
                                        </li>
                                    </ul>
                                </nav>
                                <div id="filter-data-container" class="form-group px-4">
                                    <input type="date" value="<?php echo $_GET["regi_data"]; ?>" class="form-control" placeholder="Data de pagamento" id="filter-data"></input>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row gy-2 px-4">
    
                        <div class="col-12">
                            <div class="card border rounded bg-white">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-1 px-4 py-1 bg-white d-flex align-items-center justify-content-center border-end">
                                            <!-- <input 
                                                class="form-check-input mt-0" 
                                                type="checkbox" 
                                                aria-label="Checkbox for following text input"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"
                                            > -->
                                            <button id="edit-registro-1"
                                                type="button" 
                                                class="btn btn-link py-0"
                                                data-bs-toggle="modal" data-bs-target="#edit-modal"
                                            >
                                                <span class="mdi mdi-pencil"></span>
                                            </button>
                                        </div>
        
                                        <div class="col-2 px-4 py-1 bg-white border-end">
                                            <p id="status-registro-1" class="mb-0 text-muted" status="0" ><span class="text-primary fw-semibold">Pago</span> <small id="status-registro-data-1" date="2022-02-01" >01/02/22</small></p>
                                        </div>
        
                                        
                                        <div class="col px-4 py-1 bg-white border-end">
                                            <p id="describe-registro-1" class="mb-0" describe="Adesão transf. Santa">Adesão transf. Santa</p>
                                        </div>
                                        
                                        <div class="col-2 px-4 py-1 bg-white border-end text-center">
                                            <p id="value-registro-1" class="mb-0 text-success fw-semibold" value="350.00" >R$ 350,00</p>
                                        </div>

                                        <div class="col-2 px-4 py-1 bg-white text-center">
                                            <small class="mb-0 text-muted">01/02/22</small>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Criação -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-white">
        <div class="modal-header bg-white">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Criar Registro</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form>
        <div class="modal-body bg-white">
                <div class="mb-3 form-floating">
                    <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                        <?php 
                            foreach ($representantes as $representante) { ?>
                                <option 
                                    <?php echo $_GET["vend_id"] == $representante["id"] ? "selected" : ""; ?>
                                    value="<?php echo @$representante["id"] ?>"
                                >
                                    <?php echo @$representante["representante"]; ?>
                                </option>
                            <?php }
                        ?>

                    </select>
                    <label for="floatingSelect">Selecionar vendedor</label>
                </div>
                <div class="row mb-3 align-items-center">   
                    <div class="col-8">
                        <div class="input-group">
                            <!-- <label class="sr-only" for="exampleInputAmount">Amount (in Swiss Francs)</label> -->
                            <div class="input-group">
                            <div class="input-group-text">R$</div>
                            <input type="number" min="0.00" step="0.50" id="exampleInputAmount" class="form-control" placeholder="00,00">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check">
                            <input 
                                id="exampleCheck1"
                                type="checkbox" 
                                class="form-check-input" 
                                data-bs-toggle="collapse" data-bs-target="#data-pagamento-new-container"
                            >
                            <label class="form-check-label" for="exampleCheck1">Já está pago</label>
                        </div>
                    </div>
                </div>
                <div id="data-pagamento-new-container" class="mb-3 form-floating accordion-collapse collapse">
                    <input 
                        value="<?php echo date("Y-m-d"); ?>"
                        type="date" 
                        class="form-control" 
                        placeholder="Data de pagamento" 
                        id="data-pagamento-new"
                    ></input>
                    <label for="floatingTextarea">Data de pagamennto</label>
                </div>
                <div class="mb-3 form-floating">
                    <textarea style="height: 100px" class="form-control" placeholder="Descrição do registro..." id="floatingTextarea"></textarea>
                    <label for="floatingTextarea">Descrição</label>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
        </div>
    </div>
    </div>

        <!-- Modal Edição -->
    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-white">
        <div class="modal-header bg-white">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Registro</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form>
        <div class="modal-body bg-white">
                <div class="mb-3 form-floating">
                    <select id="edit-selected-id" class="form-select" id="floatingSelect" aria-label="Floating label select example">
                        <?php 
                            foreach ($representantes as $representante) { ?>
                                <option 
                                    <?php echo $_GET["vend_id"] == $representante["id"] ? "selected" : ""; ?>
                                    value="<?php echo @$representante["id"] ?>"
                                >
                                    <?php echo @$representante["representante"]; ?>
                                </option>
                            <?php }
                        ?>

                    </select>
                    <label for="floatingSelect">Selecionar vendedor</label>
                </div>
                <div class="row mb-3 align-items-center">   
                    <div class="col-8">
                        <div class="input-group">
                            <!-- <label class="sr-only" for="exampleInputAmount">Amount (in Swiss Francs)</label> -->
                            <div class="input-group">
                            <div class="input-group-text">R$</div>
                            <input id="edit-input-valor" type="number" min="0.00" step="0.50" id="exampleInputAmount" class="form-control" placeholder="00,00">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check">
                            <input 
                                id="edit-checkbox-status" 
                                type="checkbox" class="form-check-input"
                                data-bs-toggle="collapse" data-bs-target="#data-pagamento-edit-container"
                            >
                            <label class="form-check-label" for="exampleCheck1">Já está pago</label>
                        </div>
                    </div>
                </div>
                <div id="data-pagamento-edit-container" class="mb-3 form-floating accordion-collapse collapse">
                    <input type="date"  class="form-control" placeholder="Data de pagamento" id="data-pagamento-edit"></input>
                    <label for="floatingTextarea">Data de pagamennto</label>
                </div>
                <div class="mb-3 form-floating">
                    <textarea id="edit-textarea-describe" style="height: 100px" class="form-control" placeholder="Leave a comment here"></textarea>
                    <label for="floatingTextarea">Descrição</label>
                </div>
            </div>
            <div class="modal-footer bg-white w-100">
                <div class="w-100">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Remover</button>
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                    <button type="button" class="btn btn-secondary float-end me-2" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </form>
        </div>
    </div>
    </div>

                                            
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>
    <script>
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    </script>

    <script>
        function searchParam(key) {
            const url = new URL(window.location);
            return url.searchParams.get(key)
        }

        function insertParam(key,value) {
            const url = new URL(window.location);
            url.searchParams.set(key, value);
            window.history.pushState(null, '', url.toString());
        }
    </script>

    <script>
        const searchEl = document.getElementById("search-input");
        document.addEventListener('keydown', function ( e ) {
            if ( e.keyCode == 111 && e.ctrlKey ) { // Ctrl+/
                searchEl.focus();    
            }
        });
    </script>

    <script>

        document.querySelectorAll('[id^=edit-registro]')?.forEach(el => {
            el.addEventListener("click", () => {
                const id = (el.id).split("-")[2];
                insertParam("regi_edit", id)

                document.getElementById("edit-selected-id").value = id;

                const { value: { value } } = document.getElementById("value-registro-"+id).attributes;
                const { status: { value: status } } = document.getElementById("status-registro-"+id).attributes;
                const { describe: { value: describe } } = document.getElementById("describe-registro-"+id).attributes;
                
                document.getElementById("edit-input-valor").value = value;
                document.getElementById("edit-textarea-describe").value = describe;
                // 2022/10/02
                
                
                const checkboxEl = document.getElementById("edit-checkbox-status");
                if (!checkboxEl?.checked && (status == 0)) {
                    checkboxEl.click(); 
                } else if (checkboxEl?.checked && (status == 1)) {
                    checkboxEl.click(); 
                }
                
                const { date: { value: date } } = document.getElementById("status-registro-data-"+id).attributes;
          

                document.getElementById("data-pagamento-edit").value = date;

                console.log({ value, describe, status, date });
            })
        })

    </script>

    <script>
        const filterDateEl = document.getElementById("filter-data");
        filterDateEl.addEventListener("change", function() {
            insertParam("regi_data", filterDateEl.value);
            location.reload();
        })
    </script>

</body>
    </html>