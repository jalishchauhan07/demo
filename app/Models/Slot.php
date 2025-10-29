// app/Models/Slot.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = ['slot_date', 'slot_time', 'pickup_point', 'appointment'];

    protected $casts = [
        'appointment' => 'array',
        'slot_date' => 'date',
    ];
}
