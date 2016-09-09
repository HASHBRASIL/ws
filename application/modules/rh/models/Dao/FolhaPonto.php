<?php
/**
 * @author Ellyson de Jesus
 * @since 01/08/2014
 */
class Rh_Model_Dao_FolhaPonto
{
	private $data;
	private $feriado;
	private $cargaHoraria;
	private $folga = true;
	private $idFuncionario;
	private $tempo;
	private $horarioPadrao;
	private $calculoPonto;
	
	public function __construct($data, $idFuncionario)
	{
		$this->tempo = array();
		$this->idFuncionario = $idFuncionario;
		$this->setData($data);
		$this->verificarFolga();
	}
	public function getData()
	{
		return $this->data;
	}
	
	public function setData($data)
	{
		$this->data = $data;
		$this->setFeriado();
		$this->verificarFolga();
	}
	
	public function getIdFuncionario(){
		return $this->idFuncionario;
	}
	
	public function getFeriado()
	{
		return $this->feriado;
	}

	public function setFeriado()
	{
		$feriadosBo = new Rh_Model_Bo_Feriado();
		$feriado = $feriadosBo->find(array("data = ?" => $this->data, "ativo = ?" => App_Model_Dao_Abstract::ATIVO))->current();
		if(!empty($feriado)){
			return $this->feriado = $feriado['descricao'];
		}
		return $this->feriado;
	}
	
	public function getTempo()
	{
		return $this->tempo;
	}
	
	public function getOnlyTempo()
	{
		$tempo = array();
		$tempo['entrada1'] 	= isset($this->tempo[0])?$this->tempo[0]['hora']: null;
		$tempo['saida1'] 	= isset($this->tempo[1])?$this->tempo[1]['hora']: null;
		$tempo['entrada2'] 	= isset($this->tempo[2])?$this->tempo[2]['hora']: null;
		$tempo['saida2'] 	= isset($this->tempo[3])? $this->tempo[3]['hora']: null;
		return $tempo;
	}
	
	public function setTempo($hora, $idTempo, $descricao = null, $justificativa = null, $posicao = null)
	{
		if(empty($posicao)){
			$posicao = count($this->tempo);
		}
		$this->tempo[$posicao] = array('hora' => $hora,'idTempo' => $idTempo,'descricao'=>$descricao,  'justificativa' => $justificativa);
	}
	
	public function unsetDuplicado()
	{
		$dataInicialDiff	= new DateTime($this->data);
		$dadosPontoBo		= new Rh_Model_Bo_DadosPonto();
		foreach ($this->tempo as $key => $tempo){
			if(!empty($tempo['hora'])){
		    	$dataCompare = new DateTime( $this->data.' '.$tempo['hora'] );
			    $diff = $dataInicialDiff->diff( $dataCompare );
			    if($diff->d == 0 && $diff->h == 0 && $diff->i < 10){
			        $dadosPonto = $dadosPontoBo->get($tempo['idTempo']);
			        if($dadosPonto->duplicado != Rh_Model_Bo_DadosPonto::DUPLICADO_APROVADO){
				        $dadosPonto->duplicado = Rh_Model_Bo_DadosPonto::DUPLICADO;
				        $dadosPonto->save();
				        unset($this->tempo[$key]);
			        }
			    }
			    $dataInicialDiff = $dataCompare;
			}
		}
	}
	
	public function verificarFolga()
	{
		$this->horarioPadraoByDia();
		$this->folga = false;
		if(empty($this->horarioPadrao)){
			$this->folga = true;
		}
		if(empty($this->horarioPadrao['entrada1']) && empty($this->horarioPadrao['entrada2']) && empty($this->horarioPadrao['saida1']) && empty($this->horarioPadrao['saida2'])){
			$this->folga = true;
		}
	}
	
	public function getFolga(){
		return $this->folga;
	}
	
	public function horarioPadraoByDia()
	{
		$configHorarioBo	= new Rh_Model_Bo_ConfigHorario();
		$this->horarioPadrao = $configHorarioBo->horarioPadraoByDia($this->data, $this->idFuncionario);
	}
	
	public function getHorarioPadrao()
	{
		return $this->horarioPadrao;
	}
	
	public function sumChTrabalhado($format = "H:i:s")
	{
	    $sum 		= null;
		$intervalo 	= null;
		$i 			= 1;
		foreach ($this->tempo as $key => $tempo){
			if(empty($tempo['hora'])){
				continue;
			}
			if($i == 1){
				$i++;
				$inicio = DateTime::createFromFormat('H:i:s', $tempo['hora']);
				
			}elseif ($i == 2){
				$fim			 = DateTime::createFromFormat('H:i:s', $tempo['hora']);
				if(!empty($this->horarioPadrao['fechamento'])){
					$dataFechamento	 = DateTime::createFromFormat('H:i:s', $this->horarioPadrao['fechamento']);
					$dataFimDia		 = DateTime::createFromFormat('H:i:s', '00:00:00');
					if($dataFimDia < $fim && $dataFechamento > $fim){
						$fim->modify('+1 day');
					}
				}
				$intervalo = $inicio->diff($fim);
				$intervalo->invert = 0;
				if($sum){
					$sum = $sum->add($intervalo);
				}else{
					$sum = DateTime::createFromFormat('H:i:s', $intervalo->format('%H:%I:%S'));
				}
				$i = 1;
			}
		}
		return $sum? $sum->format($format): null;
    }
    
    public function sumChDefault($format = "H:i:s")
    {
    	$sum = null;
    	$intervalo = null;
    	if(!empty($this->horarioPadrao['entrada1']) && $this->horarioPadrao['saida1']){
    		$inicio = DateTime::createFromFormat('H:i:s', $this->horarioPadrao['entrada1']);
    		$fim = DateTime::createFromFormat('H:i:s', $this->horarioPadrao['saida1']);
    		$intervalo = $inicio->diff($fim);
    		$sum = DateTime::createFromFormat('H:i:s', $intervalo->format('%H:%I:%S'));
    	}
   		if(!empty($this->horarioPadrao['entrada2']) && $this->horarioPadrao['saida2']){
    		$inicio = DateTime::createFromFormat('H:i:s', $this->horarioPadrao['entrada2']);
    		$fim = DateTime::createFromFormat('H:i:s', $this->horarioPadrao['saida2']);
    		$intervalo = $inicio->diff($fim);
    		$sum = $sum->add($intervalo);
    	}
    	return $sum? $sum->format($format): null;
    }
    
    public function calcularPonto()
    {
    	$calculoPonto 		= new Rh_Model_Bo_CalculoPonto();
    	$this->calculoPonto = $calculoPonto->calcularPonto($this);
    }
    
    public function getCalcularPonto($nome)
    {
    	return $this->calculoPonto[$nome];
    }
    
    
	
}