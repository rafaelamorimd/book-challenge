<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\BookDTO;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Repository\AuthorRepository;
use App\Repository\SubjectRepository;
use App\Services\BookService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class BookController extends Controller
{
    use HandlesApiResponses;

    private const PER_PAGE = 10;
    private const FILTERS = ['search', 'editora', 'edicao', 'ano', 'valor', 'codl'];

    public function __construct(
        private readonly BookService $bookService,
        private readonly AuthorRepository $authorRepository,
        private readonly SubjectRepository $subjectRepository
    ) {
    }

    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $filters = $this->getFiltersFromRequest($request);
        $books = $this->bookService->getPaginatedBooks($filters, self::PER_PAGE)
            ->withQueryString();

        return $this->handleIndexResponse(
            request: $request,
            paginatedData: $books,
            filters: $request->only(self::FILTERS),
            view: 'Books/Index',
            filterKey: 'books'
        );
    }

    public function create(): InertiaResponse|JsonResponse
    {
        $data = [
            'allAuthors' => $this->authorRepository->getAllWithRelations(),
            'allSubjects' => $this->subjectRepository->getAllWithRelations(),
        ];

        return $this->handleEditResponse(
            request: request(),
            data: null,
            additionalData: $data,
            view: 'Books/Form'
        );
    }

    public function store(StoreBookRequest $request): RedirectResponse|JsonResponse
    {
        $dto = BookDTO::fromRequest($request->validated());
        $book = $this->bookService->createBook($dto);
        $bookDTO = BookDTO::fromModel($book);

        return $this->handleStoreResponse(
            request: $request,
            data: $bookDTO,
            message: 'Livro criado com sucesso!',
            redirectRoute: 'books.index'
        );
    }

    public function edit(Book $book): InertiaResponse|JsonResponse
    {
        $book = $this->bookService->getBook($book);
        $bookDTO = BookDTO::fromModel($book);

        $additionalData = [
            'allAuthors' => $this->authorRepository->getAllWithRelations(),
            'allSubjects' => $this->subjectRepository->getAllWithRelations(),
        ];

        return $this->handleEditResponse(
            request: request(),
            data: $bookDTO,
            additionalData: $additionalData,
            view: 'Books/Form',
            dataKey: 'book'
        );
    }

    public function show(Book $book): InertiaResponse|JsonResponse
    {
        $book = $this->bookService->getBook($book);
        $bookDTO = BookDTO::fromModel($book);

        $additionalData = [
            'allAuthors' => $this->authorRepository->getAllWithRelations(),
            'allSubjects' => $this->subjectRepository->getAllWithRelations(),
        ];

        return $this->handleEditResponse(
            request: request(),
            data: $bookDTO,
            additionalData: $additionalData,
            view: 'Books/Form',
            dataKey: 'book'
        );
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse|JsonResponse
    {
        $dto = BookDTO::fromRequest($request->validated());
        $updatedBook = $this->bookService->updateBook($book, $dto);
        $bookDTO = BookDTO::fromModel($updatedBook);

        return $this->handleUpdateResponse(
            request: $request,
            data: $bookDTO,
            message: 'Livro atualizado com sucesso!',
            redirectRoute: 'books.index'
        );
    }

    public function destroy(Book $book): RedirectResponse|JsonResponse
    {
        $this->bookService->deleteBook($book);

        return $this->handleDestroyResponse(
            request: request(),
            message: 'Livro excluído com sucesso!',
            redirectRoute: 'books.index'
        );
    }

    private function getFiltersFromRequest(Request $request): array
    {
        $filters = [];
        foreach (self::FILTERS as $filter) {
            if ($request->has($filter)) {
                $filters[$filter] = $request->input($filter);
            }
        }
        return $filters;
    }
}
