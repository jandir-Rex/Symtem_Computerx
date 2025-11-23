<?php

namespace App\Events;

use App\Models\Producto;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class StockActualizado implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $producto;

    /**
     * Crear una nueva instancia del evento.
     *
     * @param \App\Models\Producto $producto
     */
    public function __construct(Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Canal donde se transmitirá el evento.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('almacen'); // el canal donde escuchará el front-end
    }

    /**
     * Nombre del evento en el front-end.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'stock.actualizado';
    }

    /**
     * Datos que se enviarán al front-end.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'producto' => [
                'id' => $this->producto->id,
                'stock' => $this->producto->stock
            ]
        ];
    }
}
