.. highlight:: php

***********
CashewModel
***********

Cashew lleva incorporado un sencillo ORM, que permite que los modelos se mapeen 
automáticamente con la base de datos.

Creación de un modelo
=====================

Cashew utiliza por defecto una nomenclatura estrica para los modelos. El nombre 
de cada modelo tiene que estar definido en **singular** y su tabla correspondiende 
en la base de datos tiene que estar en **plural**.

Por ejemplo, definimos el siguiente modelo::

   <?php
   class Model extends CashewModel
   {
   }
   
Su tabla correspondiente en la base de datos tendría que estar generada por la 
siguiente migración::

   <?php
   class Migration_Add_models extends CI_Migration
   {
       public function up()
       {
           // Aquí se definenen los campos...
           $this->dbforge->create_table('models', true);
       }
   }

Los atributos que tenga el modelo se tienen que definir como campos en la tabla
de la base de datos, ya que automáticamente estos serán mapeados a atributos 
del modelo.

Definir Atributos
-----------------

Los modelos tienen que tener los siguientes atributos definidos de forma 
obligatoria:

* ``id`` Identificador del modelo, que tiene que ser definido como entero, 
  clave primaria y autoincremental.

También se pueden definir los siguientes atributos que serán tratados de forma 
automática, aunque son opcionales:

* ``created_at`` Fecha de creación del modelo.
* ``updated_at`` Fecha de la última actualización del modelo.

Así pues, una migración mínima que contengan todos estos parámetros, quedaría 
de la siguiente forma::

   <?php
   class Migration_Add_models extends CI_Migration
   {
       public function up()
       {
           $this->dbforge->add_field(array(
               'id' => array(
                   'type' => 'INT',
                   'constraint' => 11,
                   'unsigned' => TRUE,
                   'auto_increment' => TRUE
               ),
               'created_at' => array(
                   'type' => 'DATETIME',
               ),
               'updated_at' => array(
                   'type' => 'DATETIME',
               ),
           $this->dbforge->add_key('id', TRUE);
           $this->dbforge->create_table('models', true);
       }
       public function down()
       {
           $this->dbforge->drop_table('models');
       }
   }
   
Métodos del modelo
------------------

.. class:: CashewModel

.. method:: CashewModel CashewModel->get($id)

Obtiene la instancia del modelo que tiene como identificador el que se le pasa 
por parámetro. ::

   <?php
   // Suponemos que estamos en el método de un controlador...
   $model = $this->Model->get($id);

Devuelve ``null`` si no existe ningún modelo con el identificador indicado.

.. method:: CashewModel CashewModel->get_by($attribute, $value)

Obtiene la instancia del modelo que tiene el valor ``$value`` en el atributo 
``$attribute``. En caso de que haya más de una ocurrencia sólo se obtiene el 
primer resultado. Devuelve ``null`` si no existe ningún modelo con ese 
parámetro. ::

   <?php
   // Suponemos que estamos en el método de un controlador...
   $model = $this->Model->get_by('att', $value);

.. method:: Array CashewModel->filter(array('attribute' => 'value', [...]))

Realiza una búsqueda entre todos los modelos almacenados en la base de datos 
y devuelve un array con aquellos que cumplen los criterios.

Para indicar los criterios se utiliza un array asociativo donde el índice es el 
nombre del atributo, y el valor es el valor que tiene que tener. ::

   <?php
   // Suponemos que estamos en el método de un controlador...
   $models = $this->Model->filter(array('att' => $value));

.. method:: bool CashewModel->save()

Guarda el modelo actual en la base de datos. Devuelve ``true`` si se ha guardado 
correctamente y ``false`` en caso contrario.
