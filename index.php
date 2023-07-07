<?php
//https://www.youtube.com/watch?v=hKrGWDggObU&t=11s&ab_channel=C%C3%B3digoyCaf%C3%A9

/**
 * 
 */

abstract class OperationAbstract
{
    protected $operation;

    /**
     *Setea la proxima operacion a ejecutar.
     *
     * @param  OperationAbstract $operation Guarda la proxima operacion a ejecutar en su instancia.
     * 
     */

    public function then(OperationAbstract $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * Verifica si existe una operacion seteada (si la cadena tiene mas elabones que procesar). 
     * Si existe, entonces envia a procesar la siguiente operacion (Handler).
     * Si no existe, entonces la cadena fue procesada o se cumplio el objetivo en eslabon anterior.
     * Llama al proximo eslabon de la cadena.
     * 
     *@param Transaction $transaction Enviada a la siguiente a la operacion.
     *@return void
     */

    public function next(Transaction $transaction): void
    {
        //valida que exista un handler para poder continuar.
        if ($this->operation)
            $this->operation->process($transaction);
    }

    /**
     * Todas las operaciones (Handlers) deberan contar con el metodo process.
     * 
     *@param Transaction $transaction Enviada a la siguiente a la operacion.
     *@return void
     */

    abstract function process(Transaction $transaction): void;
}

/**
 * Handler responsable de chequear que la transaccion sea multiplo de $100. Ya que es el billete mas chico 
 * que el cajero nos puede entregar
 */

class MultipleOneHundred extends OperationAbstract
{
    /**
     * process: Metodo abstracto. Requiere implementacion.
     * Si la cantidad no es multipo de $100, imprime un mensaje y termina. Caso contrario encadena el proximo Handler.
     *
     * @param  Transaction $transaction
     * @return void
     */

    public function process(Transaction $transaction): void
    {
        if ($transaction->getAmount() % 100 <> 0) {
            echo "La cantidad debe ser multiplo de $100\n";
            return;
        }
        //Metodo implementado en la clase Padre.
        $this->next($transaction);
    }
}

/**
 * Handler responsable de chequear si existe saldo suficiente para realizar la transaccion.
 */

class BalanceChecker extends OperationAbstract
{
    /**
     * process: Metodo abstracto. Requiere implementacion.
     * Si el saldo no es suficiente, imprime un mensaje y termina. Caso contrario encadena el proximo Handler.
     *
     * @param  Transaction $transaction
     * @return void
     */
    public function process(Transaction $transaction): void
    {
        if ($transaction->getBalance() < $transaction->getAmount()) {
            echo "El saldo no es suficiente para ejecutar la transaccion. \n";
            return;
        }

        $this->next($transaction);
    }
}

/**
 * Billetes $500. 
 * Handler responsabe de calcular la cantidad de billetes de $500 a entregar.
 */

class FiveHundredBillDeliver extends OperationAbstract
{
    /**
     * process: Metodo abstracto. Requiere implementacion.
     * Calcula la cantidad de billete sde $500 a entregar.
     * Decrementa el saldo en base a la cantidad calculada anteriormente.
     *
     * @param  Transaction $transaction
     * @return void
     */
    public function process(Transaction $transaction): void
    {
        //Si la cantidad es menor a 500, llamo la proximo handler
        if ($transaction->getAmount() < 500) {
            $this->next($transaction);

            return;
        }

        //Calcula la cantidad de billetes a entregar.
        $billsToDeliver = intval($transaction->getAmount() / 500);
        //Calcula si queda dinero pendiente para entregar.
        $billsToRemains = $transaction->getAmount() % 500;
        echo "Entrega {$billsToDeliver} billetes de $500\n";

        if ($billsToRemains != 0) {
            //Actualizo la cantidad de dinero requerida con el valor del dinero pendiente a entregar.
            $transaction->setAmount($billsToRemains);
            $this->next($transaction);
        }
    }
}

/**
 * Billetes $200. 
 * Handler responsabe de calcular la cantidad de billetes de $200 a entregar.
 */

class TwoHundredBillDeliver extends OperationAbstract
{
    /**
     * process: Metodo abstracto. Requiere implementacion.
     * Calcula la cantidad de billete sde $200 a entregar.
     * Decrementa el saldo en base a la cantidad calculada anteriormente.
     *
     * @param  Transaction $transaction
     * @return void
     */
    public function process(Transaction $transaction): void
    {
        //Si la cantidad es menor a 200, llamo la proximo handler
        if ($transaction->getAmount() < 200) {
            $this->next($transaction);

            return;
        }

        //Calcula la cantidad de billetes a entregar.
        $billsToDeliver = intval($transaction->getAmount() / 200);
        //Calcula si queda dinero pendiente para entregar.
        $billsToRemains = $transaction->getAmount() % 200;
        echo "Entrega {$billsToDeliver} billetes de $200\n";

        if ($billsToRemains != 0) {
            //Actualizo la cantidad de dinero requerida con el valor del dinero pendiente a entregar.
            $transaction->setAmount($billsToRemains);
            $this->next($transaction);
        }
    }
}

/**
 * 
 * Billetes $100. 
 * Handler responsabe de calcular la cantidad de billetes de $100 a entregar.
 *  
 */

class OneHundredBillDeliver extends OperationAbstract
{
    /**
     * process: Metodo abstracto. Requiere implementacion.
     * Calcula la cantidad de billete sde $100 a entregar.
     * Decrementa el saldo en base a la cantidad calculada anteriormente.
     *
     * @param  Transaction $transaction
     * @return void
     */
    public function process(Transaction $transaction): void
    {
        //Si la cantidad es menor a 100, llamo la proximo handler
        if ($transaction->getAmount() < 100) {
            $this->next($transaction);

            return;
        }

        //Calcula la cantidad de billetes a entregar.
        $billsToDeliver = intval($transaction->getAmount() / 100);
        //Calcula si queda dinero pendiente para entregar.
        $billsToRemains = $transaction->getAmount() % 100;
        echo "Entrega {$billsToDeliver} billetes de $100\n";

        if ($billsToRemains == 0) {
            //Actualizo la cantidad de dinero requerida con el valor del dinero pendiente a entregar.
            $transaction->setAmount($billsToRemains);
            echo "Operacion Finalizada";
        }
    }
}

/**
 * Transaccion. Extraccion de dinero realizada por el cajero automatico.
 * 
 * @var int $amount Cantidad de dinero a extraer de la cuenta.
 * @var int $balance Cantidad de dinero dispobible en la cuenta.  
 * 
 */
class Transaction
{
    protected int $amount;
    protected int $balance;

    public function __construct(int $balance, int $amount)
    {
        $this->amount = $amount;
        $this->balance = $balance;
    }
    public function process(Transaction $transaction): void
    {
        # code...
    }
    public function setAmount($amound): void
    {
        $this->amount = $amound;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setBalance($balance): void
    {
        $this->balance = $balance;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

$transaction = new Transaction(1500, 1300);

$multipleHandler = new MultipleOneHundred();
$balanceHandler = new BalanceChecker();
$fiveHundredHandler = new FiveHundredBillDeliver();
$twoHundredHandler = new TwoHundredBillDeliver();
$oneHundredHandler = new OneHundredBillDeliver();

$multipleHandler->then($balanceHandler);
$balanceHandler->then($fiveHundredHandler);
$fiveHundredHandler->then($twoHundredHandler);
$twoHundredHandler->then($oneHundredHandler);
$multipleHandler->process($transaction);
