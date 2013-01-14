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
     * @param (int|string)[] $answers
     *
     * @return boolean
     */
    public static function save(array $answers, Database &$database)
    {
        $options = array(
            'dificuldade' => FILTER_SANITIZE_STRING,
            'explicacao_dificuldade' => FILTER_SANITIZE_STRING,
            'encontrou' => FILTER_SANITIZE_STRING,
            'aluno_EACH' => FILTER_SANITIZE_STRING,
            'indicaria' => FILTER_SANITIZE_STRING,
            'referencia' => FILTER_SANITIZE_STRING,
            'nota_design' => FILTER_SANITIZE_NUMBER_INT,
            'nota_funcionalidades' => FILTER_SANITIZE_NUMBER_INT,
            'nota_acessibilidade' => FILTER_SANITIZE_NUMBER_INT,
            'nota_insercao_reps' => FILTER_SANITIZE_NUMBER_INT,
            'info_adicional' => FILTER_SANITIZE_STRING
        );

        $answers = filter_var_array($answers, $options);

        $query = $database->prepare('
            INSERT INTO feedback (
                dificuldade, explicacao_dificuldade, encontrou, aluno_EACH,
                indicaria, referencia, nota_design, nota_funcionalidades,
                nota_acessibilidade, nota_insercao_reps, info_adicional
            ) VALUES (
                :dificuldade, :explicacao_dificuldade, :encontrou, :aluno_EACH,
                :indicaria, :referencia, :nota_design, :nota_funcionalidades,
                :nota_acessibilidade, :nota_insercao_reps, :info_adicional
            )
        ');

        do {
            $query->bindParam(':'.key($answers), current($answers));
        } while (next($answers) !== false);

        return $query->execute();
    }
}

