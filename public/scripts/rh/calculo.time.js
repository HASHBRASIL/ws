/**
 * Realiza os cálculos dos horários, com base nas horas informadas.
 * adaptado de: http://www.sistemaseweb.com.br/computacaoemfoco/CalculadorHoras.html
 */
function calculaHoras(horaEntrada,horaIdaAlmoco,horaVoltaAlmoco,horaSaida){
	
	// valores inseridos
//	totalHorasDia = $("#totalHorasDia").val();
//	horaEntrada = $("#horaEntrada").val();
//	horaIdaAlmoco = $("#horaIdaAlmoco").val();
//	horaVoltaAlmoco = $("#horaVoltaAlmoco").val();	
//	horaSaida = $("#horaSaida").val();
	var diffEntradaAlmoco = null;
	var diffSaidaHora = null;
	var totalHora = null;
	
	if( possuiValor(horaEntrada) && possuiValor(horaIdaAlmoco) ) {
		
		// Calcula o intervalo entre a entrada e o almoco
		diffEntradaAlmoco = diferencaHoras( horaEntrada, horaIdaAlmoco );
		totalHora = diffEntradaAlmoco;
	}
	
	if( possuiValor(horaVoltaAlmoco) && possuiValor(horaSaida) ) {
		// Calcula o intervalo entre a entrada e o almoco
		diffSaidaHora = diferencaHoras( horaVoltaAlmoco, horaSaida );
		totalHora = diffSaidaHora;
	}
	if( possuiValor(diffEntradaAlmoco) && possuiValor(diffSaidaHora) ) {
		// Calcula o intervalo entre a entrada e o almoco
		totalHora = somaHora(diffEntradaAlmoco,diffSaidaHora);
	}
	return totalHora;
}

/**
* Retona a diferença entre duas horas.
* Exemplo: 14:35 a 17:21 = 02:46
* Adaptada de http://stackoverflow.com/questions/2053057/doing-time-subtraction-with-jquery
*/
function diferencaHoras(horaInicial, horaFinal) {

	// Tratamento se a hora inicial é menor que a final	
	if( ! isHoraInicialMenorHoraFinal(horaInicial, horaFinal) ){
	 	aux = horaFinal;
		horaFinal = horaInicial;
		horaInicial = aux;
	}

    hIni = horaInicial.split(':');
    hFim = horaFinal.split(':');

	horasTotal = parseInt(hFim[0], 10) - parseInt(hIni[0], 10);
	minutosTotal = parseInt(hFim[1], 10) - parseInt(hIni[1], 10);
	
    if(minutosTotal < 0){
        minutosTotal += 60;
        horasTotal -= 1;
    }
	
    horaFinal = completaZeroEsquerda(horasTotal) + ":" + completaZeroEsquerda(minutosTotal);
    return horaFinal;
}

/**
* Soma duas horas.
* Exemplo:  12:35 + 07:20 = 19:55.
*/
function somaHora(horaInicio, horaSomada) {
	
    horaIni = horaInicio.split(':');
    horaSom = horaSomada.split(':');

    horasTotal = parseInt(horaIni[0], 10) + parseInt(horaSom[0], 10);
	minutosTotal = parseInt(horaIni[1], 10) + parseInt(horaSom[1], 10);
	
    if(minutosTotal >= 60){
        minutosTotal -= 60;
        horasTotal += 1;
    }
	
    horaFinal = completaZeroEsquerda(horasTotal) + ":" + completaZeroEsquerda(minutosTotal);
    return horaFinal;
}

/**
 * Verifica se a hora inicial é menor que a final.
 */
function isHoraInicialMenorHoraFinal(horaInicial, horaFinal){
	horaIni = horaInicial.split(':');
    horaFim = horaFinal.split(':');

	// Verifica as horas. Se forem diferentes, é só ver se a inicial 
	// é menor que a final.
	hIni = parseInt(horaIni[0], 10);
	hFim = parseInt(horaFim[0], 10);
	if(hIni != hFim)
		return hIni < hFim;
	
	// Se as horas são iguais, verifica os minutos então.
    mIni = parseInt(horaIni[1], 10);
	mFim = parseInt(horaFim[1], 10);
	if(mIni != mFim)
		return mIni < mFim;
}

/**
 * Verifica se o usuário informou todos os campos da hora (hh:mm).
 */
function preencheuHoraCompleta( horario ){
	var hora = horario.replace(/[^0-9:]/g ,''); // deixa só números e o ponto
	return hora.length == 5;
}

/**
 * Verifica se a hora é válidas. Exemplo: 12:34 é válido, 03:89 é inválido.
 */
function isHoraValida( horario ) {
	var regex = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9])$");
	return regex.exec( horario ) != null;
}

/**
 * Verifica se um campo está vazio.
 */
function possuiValor( valor ){
	return valor != undefined && valor != '';
}

/**
 * Completa um número menor que dez com um zero à esquerda.
 * Usado aqui para formatar as horas... Exemplo: 3:10 -> 03:10 , 10:5 -> 10:05
 */
function completaZeroEsquerda( numero ){
	return ( numero < 10 ? "0" + numero : numero);
}
