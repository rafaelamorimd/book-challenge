declare(strict_types=1);

namespace App\Services;

use App\Models\ReportAuthor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportAuthorService
{
    public function getPaginatedReports(
        array $filters,
        string $sortField,
        string $sortDirection,
        int $perPage
    ): LengthAwarePaginator {
        $query = ReportAuthor::query();

        foreach ($filters as $field => $value) {
            if ($field === 'publication_year') {
                $query->where($field, $value);
            } else {
                $query->where($field, 'ilike', '%' . $value . '%');
            }
        }

        return $query->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function getStatistics(): object
    {
        return DB::table('view_report_author')
            ->select([
                DB::raw('COUNT(DISTINCT author_id) as total_authors'),
                DB::raw('COUNT(DISTINCT book_id) as total_books'),
                DB::raw('COUNT(DISTINCT publisher) as total_publishers'),
                DB::raw('AVG(amount) as average_book_price'),
                DB::raw('MIN(publication_year) as oldest_publication'),
                DB::raw('MAX(publication_year) as newest_publication'),
            ])
            ->first();
    }

    public function getTopAuthors(int $limit): Collection
    {
        return DB::table('view_report_author')
            ->select([
                'author_id',
                'author_name',
                DB::raw('COUNT(DISTINCT book_id) as total_books'),
                DB::raw('STRING_AGG(DISTINCT subjects, \', \') as all_subjects'),
            ])
            ->groupBy('author_id', 'author_name')
            ->orderByDesc('total_books')
            ->limit($limit)
            ->get();
    }
}
