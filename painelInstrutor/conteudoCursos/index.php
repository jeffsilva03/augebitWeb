<?php
include '../../arquivosReuso/conexao.php';

// Incluir cabeçalho
require_once 'headerInstrutor.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Instrutor - Atividades</title>
    <link rel="icon" href="src/icone.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>

   
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: rgb(255, 254, 254);
        }

       
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .course-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .course-selector h2 {
            color: #374151;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .col-md-4 {
            flex: 1;
            min-width: 280px;
            padding: 10px;
        }

        .mb-3, .mb-33 {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control, .form-select {
            width: 100%;
            height: 45px;
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background-color: #ffffff;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-control1 {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background-color: #ffffff;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .question-title {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #374151;
        }

        #mensagem {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

       
    </style>
</head>

<body>
    

    <div class="container">
        <!-- Seletor de Curso -->
        <div class="course-selector">
            <h2>Selecione o Curso</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Curso</label>
                        <select id="curso-select" class="form-select" required>
                            <option value="">Selecione um curso...</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Atividades -->
        <div id="atividades-form" style="display: none;">
            <form id="form-config" enctype="multipart/form-data" method="POST" action="conexao.php">
                <input type="hidden" id="curso_id_hidden" name="curso_id" value="">
                
                <div class="form-section">
                    <div class="question-title">Informações da Atividade</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-33">
                                <label class="form-label">Título da Atividade</label>
                                <input name="tituloAtiv" type="text" class="form-control" placeholder="Título da Atividade" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questões 1-10 -->
                <div class="form-section">
                    <div class="question-title">Questão 1</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 1</label>
                                <input name="questao1" type="text" class="form-control" placeholder="Insira a questão 1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao1" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem1" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 2</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 2</label>
                                <input name="questao2" type="text" class="form-control" placeholder="Insira a questão 2" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao2" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem2" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 3</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 3</label>
                                <input name="questao3" type="text" class="form-control" placeholder="Insira a questão 3" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao3" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem3" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 4</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 4</label>
                                <input name="questao4" type="text" class="form-control" placeholder="Insira a questão 4" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao4" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem4" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 5</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 5</label>
                                <input name="questao5" type="text" class="form-control" placeholder="Insira a questão 5" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao5" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem5" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 6</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 6</label>
                                <input name="questao6" type="text" class="form-control" placeholder="Insira a questão 6" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao6" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem6" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 7</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 7</label>
                                <input name="questao7" type="text" class="form-control" placeholder="Insira a questão 7" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao7" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem7" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 8</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 8</label>
                                <input name="questao8" type="text" class="form-control" placeholder="Insira a questão 8" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao8" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem8" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 9</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 9</label>
                                <input name="questao9" type="text" class="form-control" placeholder="Insira a questão 9" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao9" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem9" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="question-title">Questão 10</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Questão 10</label>
                                <input name="questao10" type="text" class="form-control" placeholder="Insira a questão 10" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input name="descricao10" type="text" class="form-control" placeholder="Insira a descrição da questão" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input name="imagem10" type="file" class="form-control1" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="text-align: center;">
                    <button class="btn btn-primary" type="submit">Salvar Atividade</button>
                </div>

                <div id="mensagem"></div>
            </form>
        </div>
    </div>

    <script>
        // Carregar cursos ao inicializar a página
        $(document).ready(function() {
            carregarCursos();
        });

        function carregarCursos() {
            $.ajax({
                url: 'get_cursos.php',
                type: 'GET',
                dataType: 'json',
                success: function(cursos) {
                    const select = $('#curso-select');
                    select.empty().append('<option value="">Selecione um curso...</option>');
                    
                    cursos.forEach(function(curso) {
                        select.append(`<option value="${curso.id}">${curso.titulo}</option>`);
                    });
                },
                error: function() {
                    console.error('Erro ao carregar cursos');
                }
            });
        }

        // Mostrar formulário quando curso for selecionado
        $('#curso-select').change(function() {
            const cursoId = $(this).val();
            if (cursoId) {
                $('#curso_id_hidden').val(cursoId);
                $('#atividades-form').slideDown();
                carregarAtividade(cursoId);
            } else {
                $('#atividades-form').slideUp();
            }
        });

        function carregarAtividade(cursoId) {
            $.ajax({
                url: 'get_atividade.php',
                type: 'GET',
                data: { curso_id: cursoId },
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        // Preencher os campos com os dados existentes
                        $('input[name="tituloAtiv"]').val(data.tituloAtiv || '');
                        for (let i = 1; i <= 10; i++) {
                            $(`input[name="questao${i}"]`).val(data[`questao${i}`] || '');
                            $(`input[name="descricao${i}"]`).val(data[`descricao${i}`] || '');
                        }
                    }
                }
            });
        }

        // Submit do formulário
        $("#form-config").submit(function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: "conexao.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resposta) {
                    $('#mensagem').removeClass('error').addClass('success').text(resposta);
                    $('html, body').animate({ scrollTop: $('#mensagem').offset().top }, 500);
                },
                error: function() {
                    $('#mensagem').removeClass('success').addClass('error').text("Erro ao enviar os dados.");
                }
            });
        });
    </script>
</body>
</html>