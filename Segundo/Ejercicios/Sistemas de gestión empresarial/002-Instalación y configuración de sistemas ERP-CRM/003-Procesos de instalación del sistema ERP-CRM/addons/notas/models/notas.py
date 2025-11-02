from odoo import models, fields

class Notas(models.Model):
    _name = 'notas.nota'
    titulo = fields.Char(string='Título')
    contenido = fields.Text(string='Contenido')
