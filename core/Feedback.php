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
        $filterString = array(
            'filter' => FILTER_SANITIZE_STRING,
            'flags' => FILTER_FLAG_ENCODE_HIGH
        );

        $options = array(
            'dificuldade' => $filterString,
            'explicacao_dificuldade' => $filterString,
            'encontrou' => $filterString,
            'aluno' => $filterString,
            'indicaria' => $filterString,
            'referencia' => $filterString,
            'design' => FILTER_SANITIZE_NUMBER_INT,
            'funcionalidades' => FILTER_SANITIZE_NUMBER_INT,
            'acessibilidade' => FILTER_SANITIZE_NUMBER_INT,
            'inserir_rep' => FILTER_SANITIZE_NUMBER_INT,
            'info_adicional' => $filterString
        );

        $answers = filter_var_array($answers, $options);

        $options = array(
            'design' => FILTER_VALIDATE_INT,
            'funcionalidades' => FILTER_VALIDATE_INT,
            'acessibilidade' => FILTER_VALIDATE_INT,
            'inserir_rep' => FILTER_VALIDATE_INT
        );

        $valid = (bool) filter_var_array($answers, $options);

        if ($valid) {
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

        return false;
    }
}

