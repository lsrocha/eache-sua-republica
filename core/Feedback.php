<?php
namespace core;

use core\Database;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
class Feedback
{
    /**
     * @var object
     */
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @param (int|string)[] $answers
     *
     * @return boolean
     */
    public function save(array $answers)
    {
        $options = array(
            'dificuldade' => FILTER_SANITIZE_STRING,
            'explicacao_dificuldade' => FILTER_SANITIZE_STRING,
            'encontrou' => FILTER_SANITIZE_STRING,
            'aluno' => FILTER_SANITIZE_STRING,
            'indicaria' => FILTER_SANITIZE_STRING,
            'referencia' => FILTER_SANITIZE_STRING,
            'design' => FILTER_SANITIZE_NUMBER_INT,
            'funcionalidades' => FILTER_SANITIZE_NUMBER_INT,
            'acessibilidade' => FILTER_SANITIZE_NUMBER_INT,
            'inserir_rep' => FILTER_SANITIZE_NUMBER_INT,
            'info_adicional' => FILTER_SANITIZE_STRING
        );

        $answers = filter_var_array($answers, $options);

        return $this->database->basicQuery(
            "INSERT INTO feedback(
                dificuldade, explicacao_dificuldade, encontrou, aluno_EACH, 
                indicaria, referencia, nota_design, nota_funcionalidades, 
                nota_acessibilidade, nota_insercao_reps, info_adicional
            ) VALUES (
                '{$answers['dificuldade']}', '{$answers['explicacao_dificuldade']}', 
                '{$answers['aluno']}', '{$answers['encontrou']}', '{$answers['indicaria']}', 
                '{$answers['referencia']}', '{$answers['design']}', '{$answers['funcionalidades']}', 
                '{$answers['acessibilidade']}', '{$answers['inserir_rep']}', '{$answers['info_adicional']}'
            )"
        );
    }
}

