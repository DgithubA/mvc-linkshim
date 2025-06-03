<?php



namespace Lms\app\Models;

use Lms\Core\Abstracts\Model;

class Course extends Model
{
    protected static string $table = 'courses';
    public int $instructor_id;
    public int $id;
    public string $title;
    public string $description;
    public int $category;
    public string $level;
    public string $language;
    public float $price;
    public int $discount_percent;
    public int $duration_hours;
    public int $start_date;
    public int $end_date;
    public string $status;
    public string $thumbnail_url;
}
