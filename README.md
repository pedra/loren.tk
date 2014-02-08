Loren Ipsum Generator API
========

Simple API to generate Lorem Ipsolum Text

**Usage** : http://loren.tk/ [COMAND] / [OPTION] / [OPTION]

Commands & options see http://loren.tk


Usage
========

**Html iframe**

    <iframe src="http://loren.tk/p/4/1"></iframe>
    
**Jquery Ajax**

    in HTML
    <div id="ajax"> --- </div>

    in Javascript
    $("#ajax").load("http://loren.tk/p/2/1");
    
**PHP Code**

    <?php
    $loren = file_get_contents('http://loren.tk/p/10/1');
    
    echo $loren;
    //Generates 10 paragraphs with '<p>' tags.
 
    
**Populate Table in Database Mysql**
    
    <?php
    $pdo = new PDO('mysql:host=localhost;dbname=MyDbase;charset=UTF8', 'root', '******');
    $sth = $pdo->prepare('INSERT INTO yourtablename (yourTextCol) VALUES (:loren)');
    
    $sth->execute(array('loren'=>file_get_contents('http://loren.tk/w/1000')));
    
    



:)
==

Tanks Renato Cassino [https://github.com/Tacno]
