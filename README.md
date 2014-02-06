loren.tk
========

Simple API to generate Lorem Ipsolum Text

**Usage** : http://loren.tk/ [COMAND] / [OPTION] / [OPTION]

Commands & options:
 
- http://loren.tk/w/qtd       - generates words
- http://loren.tk/c/min/max   - generates characters
- http://loren.tk/p/qtd/tag   - generates paragraphs
- http://loren.tk/r/          - generates random words
- http://loren.tk/            - generates 10 random words! (default)
- http://loren.tk/help        - this help. 


Examples
========

**PHP**

    <?php
    $loren = file_get_contents('http://loren.tk/p/10/1');
    
    echo $loren;
    //Generates 10 paragraphs with '<p>' tags.
 
    
**Populate table in database mysql**
    
    <?php
    $pdo = new PDO('mysql:host=localhost;dbname=MyDbase;charset=UTF8', 'root', '******');
    $sth = $pdo->prepare('INSERT INTO table_name (column_name) VALUES (:loren)');
    
    $sth->execute(array('loren'=>file_get_contents('http://loren.tk/w/1000')));
    
    
**Html iframe**

    . . .
    <iframe src="http://loren.tk/p/4/1"></iframe>
    . . .

**Jquery Ajax**

*in HTML*

    <div id="ajax"> --- </div>
    
*in Javascript*

    $("#ajax").load("http://loren.tk/p/2/1");



:)
===

Please report bugs, suggestions here (Pull Request) and in my email [prbr#ymail.com].


-_*
===

Tanks Renato Cassino [https://github.com/Tacno]
