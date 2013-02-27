# -*- coding: utf-8 -*-
'''
Created on 05/10/2012

@author: Cuble Desarrollo S.L.
'''
from __future__ import absolute_import

from celery import Celery

# --- Configuración de Celery --- 
celery = Celery('tasks.celery',
                broker='amqp://',
                backend='amqp://',
                include=['tasks.tasks'])

# --- Configuración de Celery opcional --- 
celery.conf.update(
    CELERY_TASK_RESULT_EXPIRES=3600,
)

if __name__ == '__main__':
    celery.start()