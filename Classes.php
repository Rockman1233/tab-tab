<?php

class TaskManager{
    /** @var TaskPrototype[]  */
    private $aTasks = [];

    /**
     * @var null|TaskPrototype
     */
    public $oCurrentTask = null;

    private $sConfigTasks = [
        [
            'title' => 'Сумма чисел',
            'description' => 'Посчитать сумму двух чисел',
            'class' => TaskSumm::class,
        ],
        [
            'title' => 'Разность чисел',
            'description' => 'Посчитать разность двух чисел',
            'class' => TaskDif::class,
        ],
        [
            'title' => 'Произведение чисел',
            'description' => 'Посчитать произведение двух чисел',
            'class' => TaskMult::class,
        ],
        [
            'title' => 'Частное чисел',
            'description' => 'Посчитать частное двух чисел',
            'class' => TaskDiv::class,
        ],
        [
            'title' => 'Факториал числа n',
            'description' => 'Вычислить n!',
            'class' => TaskFacktorial::class,
        ],
        [
            'title' => 'Числа Фибоначчи',
            'description' => 'Вывести n-ый элемент численности фибоначи',
            'class' => Fibonachi::class,
        ],
        [
            'title' => 'Генерация массива',
            'description' => 'Сгенерировать одномерный массив из случайных n элементов',
            'class' => Massive_generator::class,
        ],

        [
            'title' => 'Изменение массива',
            'description' => 'Сгенерировать одномерный массив из случайных n элементов. Вывести исходный массив. Вывести отдельно четные и нечетные элементы массива',
            'class' => Massive_generator_change::class,
        ],
        [
            'title' => 'Сортировка массива. Пузырек',
            'description' => 'Сгенерировать случайный массив. Вывести изначальный массив, отсортировать его <a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%BE%D1%80%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0_%D0%BF%D1%83%D0%B7%D1%8B%D1%80%D1%8C%D0%BA%D0%BE%D0%BC">пузырьком</a>',
            'class' => Massive_generator_bubble::class,
        ],
        [
            'title' => 'Сортировка массива. Шейкер',
            'description' => 'Сгенерировать случайный массив. Вывести изначальный массив, отсортировать его <a href="https://ru.wikipedia.org/wiki/%D0%A1%D0%BE%D1%80%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0_%D0%BF%D0%B5%D1%80%D0%B5%D0%BC%D0%B5%D1%88%D0%B8%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%D0%BC">перемешиванием</a>',
            'class' => Massive_generator_shake::class,
        ]

    ];

    function __construct()
    {
        $iTask = isset($_GET['task'])? $_GET['task'] : false;
        foreach ($this->sConfigTasks as $key => $item) {
            if (isset($item['class']) && class_exists($item['class'])) {
                $this->aTasks[$key+1] = new $item['class'] (array_merge($item,['number' => $key+1]));
            }
            else{
                $this->aTasks[$key+1] = new TaskExample(array_merge($item,['number' => $key+1]));
            }
        }

        if ( $iTask && isset($this->aTasks[$iTask]) ) $this->oCurrentTask = $this->aTasks[$iTask];
    }

    public function buildMenu(){
        foreach ($this->aTasks as $oTask){
            echo
            "<div>
                <p>Задание №{$oTask->number}.<a href='?task={$oTask->number}'> {$oTask->title}</a></p>
            </div>";
        }
    }

    public function buildResult(){
       echo "<div>";

        echo "<p>Задание №{$this->oCurrentTask->number}. {$this->oCurrentTask->title} <a href='index.php'>Назад к списку</a></p>";
        echo "<p>Описание: {$this->oCurrentTask->description}</p>";
        echo "<p>Результат: </p>";
       foreach ( $this->oCurrentTask->func() as $sMessage){
            echo "<p> {$sMessage} </p>";
       }
       echo "</div>";
    }

}

abstract class  TaskPrototype
{
    public $title;
    public $number;
    public $description;

    public function __construct($params = [])
    {
        foreach ($params as $key => $value){
            if (property_exists(self::class,$key))
                $this->$key = $value;
        }

    }


    /**
     * @return string[]
     */
    abstract public function func();
}

class TaskExample extends TaskPrototype{
    public function func()
    {
        return ['Необходимо создать нужный класс с необходимыми функциями'];
    }

}

class TaskSumm extends TaskPrototype{

    public $x1 = 12;
    public $x2 = 15;

    protected function action($x1,$x2){
        return $x1+$x2;
    }

    public function func()
    {
        $out[] = "Число x1={$this->x1}";
        $out[] = "Число x2={$this->x2}";
        $out[] = $this->action($this->x1,$this->x2);
        return $out;
    }

}


class TaskMult extends TaskSumm {

    protected function action($x1,$x2){
        return $x1*$x2;
    }

}


class TaskDiv extends TaskSumm {

    protected function action($x1,$x2) {
        return $x1/$x2;
    }


}

class TaskDif extends TaskSumm {


    protected function action($x1,$x2){
        return $x1-$x2;
    }



}


class TaskFacktorial extends TaskSumm {


    protected function action($x1,$x2) //gmp_fact на моем сервере выдавал ошибку
    {
        $d = 1;
        for ($i = 1; $i <= $x1; $i++) {
            $d *= $i;
        }
        return "Факториал числа $x1 = ".$d;
    }

}


class Fibonachi extends TaskSumm {

    protected function action($num,$x2) // $num - номер интересующего нас элемента
    {
        if ($num < 1) { // номера элемента меньше 1 не существует, заканчиваем функцию
            return false;
        }
        if ($num <= 2) { // если это один из первых элементов, нетрудно увидеть как они определяются
            return ($num - 1);
        }

        // общий случай. Идем от 3го до требуемого номера
        $pre_pre = 0; // элемент, скажем так, предпредыдущий.
        $current = 1; // текущий
        for ($i = 3; $i <= $num; $i++) {
            $pre = $current; // бывший текущий становится предыдущим
            $current = $pre + $pre_pre; // определяем текущий элемент
            $pre_pre = $pre; // бывший предыдущий становится предпредыдущим
        }
        return "Под номером {$this->x1} в последовательности Фибоначи число $current";
    }

}


class Massive_generator extends TaskPrototype{

    public $x1 = 10; //решил не наследовать

    public function mas_gen($elements)
    {
        $massive = [];
        for($i=0; $i < $elements; $i++) {
            array_push($massive, rand(1, 100)); //ограничим генерацию от 0 до 100, для удобства вывода
        }
        return $massive;
    }


    public function func()
    {
        $out[] = "Генерация массива из {$this->x1} элементов.";

        foreach ($this->mas_gen($this->x1) as $index=>$value) {
            echo $index+1; // для ровности счет прибавим единичку
            echo ' -- ';
            echo $value;
            echo "<br>";
        }

        return $out;
    }

}


class Massive_generator_change extends Massive_generator {


    public function func()
    {
        $out[] = "Генерация массива из {$this->x1} элементов.";

        foreach ($this->mas_gen($this->x1) as $index=>$value) {
            if ($index%2){
                echo $index;
                echo ' -- ';
                echo $value;
                echo "<br>";
            }

        }
        echo "<br>";
        foreach ($this->mas_gen($this->x1) as $index=>$value) {
            if (!($index%2)){
                echo $index;
                echo ' -- ';
                echo $value;
                echo "<br>";
            }

        }

        return $out;
    }

}

class Massive_generator_bubble extends Massive_generator {

    public function func()
    {
        $out[] = "Генерация массива из {$this->x1} элементов.";

        $massive_1 = $this->mas_gen($this->x1); //для лучшей читаемости сгенерируем массив в отдельную переменную

        foreach ($massive_1 as $index=>$value) {
            echo $index;
            echo ' -- ';
            echo $value;
            echo "<br>";
        }
        echo "<br>";
        for($i=0; $i<count($massive_1); $i++) //перебираем исходный массив
        {
            for($j=$i+1; $j<count($massive_1); $j++) //перебираем впереди идущий элемент массива (перед основным перебором)
            {
                if($massive_1[$i]>$massive_1[$j]) // сравниваем пару элементов
                {
                    $bufer = $massive_1[$j];  //копируев в буфер наименьший
                    $massive_1[$j] = $massive_1[$i]; // меняем элементы местами
                    $massive_1[$i] = $bufer; //возвращаем буферное значение наименьшего элемента на место наибольшего
                }
            }
        }
        foreach ($massive_1 as $index=>$value) {
            echo $index;
            echo ' -- ';
            echo $value;
            echo "<br>";
        }

    }

}


class Massive_generator_shake extends Massive_generator {

    public function func()
    {
        $out[] = "Генерация массива из {$this->x1} элементов.";

        $massive_1 = $this->mas_gen($this->x1);

        foreach ($massive_1 as $index=>$value) {
            echo $index;
            echo ' -- ';
            echo $value;
            echo "<br>";
        }
        echo "<br>";

        $n = count($massive_1); //посчитаем длинну массива чтобы знать откуда выполнять итерации справа
        $left_edge = 0;
        $right_edge = $n - 1;
        do {
            for($i = $left_edge; $i < $right_edge; $i++) {
                if ($massive_1[$i] > $massive_1[$i + 1]) {
                    list($massive_1[$i], $massive_1[$i + 1]) = array($massive_1[$i + 1], $massive_1[$i]); // посмотрел функцию по ссылке в Wiki
                }
            }
            $right_edge-=1;
            for($i = $right_edge; $i > $left_edge; $i--) {
                if($massive_1[$i] < $massive_1[$i-1])
                {
                    list($massive_1[$i], $massive_1[$i - 1]) = array($massive_1[$i - 1], $massive_1[$i]);
                }
            }
            $left_edge+=1;

        }
        while($left_edge <= $right_edge);

        foreach ($massive_1 as $index=>$value) {
            echo $index;
            echo ' -- ';
            echo $value;
            echo "<br>";
        }

    }

}


