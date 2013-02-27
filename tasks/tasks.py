# -*- coding: utf-8 -*-
'''
Created on 05/10/2012

Ejemplo de tarea:

@celery.task
def add(x, y):
    return x + y


@author: Cuble Desarrollo
'''
from __future__ import absolute_import

from proj.celery import celery

# --- Añadir aquí las tareas que se quieran ejecutar en segundo plano --- 
