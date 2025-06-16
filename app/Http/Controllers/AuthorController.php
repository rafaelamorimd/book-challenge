<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\AuthorDTO;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Services\AuthorService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class AuthorController extends Controller
{
    use HandlesApiResponses;

    private const PER_PAGE = 10;
    private const FILTERS = ['search', 'nome', 'codAu'];

    public function __construct(
        private readonly AuthorService $authorService
    ) {
    }

    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $filters = $this->getFiltersFromRequest($request);
        $authors = $this->authorService->getPaginatedAuthors($filters, self::PER_PAGE)
            ->withQueryString();

        return $this->handleIndexResponse(
            request: $request,
            paginatedData: $authors,
            filters: $request->only(self::FILTERS),
            view: 'Authors/Index',
            filterKey: 'authors'
        );
    }

    public function create(): InertiaResponse
    {
        return $this->handleEditResponse(
            request: request(),
            data: null,
            additionalData: [],
            view: 'Authors/Form'
        );
    }

    public function store(StoreAuthorRequest $request): RedirectResponse|JsonResponse
    {
        $dto = AuthorDTO::fromRequest($request->validated());
        $author = $this->authorService->createAuthor($dto);
        $authorDTO = AuthorDTO::fromModel($author);

        return $this->handleStoreResponse(
            request: $request,
            data: $authorDTO,
            message: 'Autor criado com sucesso!',
            redirectRoute: 'authors.index',
            status: 201
        );
    }

    public function edit(Author $author): InertiaResponse|JsonResponse
    {
        $author = $this->authorService->getAuthor($author);
        $authorDTO = AuthorDTO::fromModel($author);
        return $this->handleEditResponse(
            request: request(),
            data: $authorDTO,
            additionalData: [],
            view: 'Authors/Form',
            dataKey: 'author'
        );
    }

    public function show(Author $author): InertiaResponse|JsonResponse
    {
        $author = $this->authorService->getAuthor($author);
        $authorDTO = AuthorDTO::fromModel($author);

        return $this->handleEditResponse(
            request: request(),
            data: $authorDTO,
            additionalData: [],
            view: 'Authors/Form',
            dataKey: 'author'
        );
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse|JsonResponse
    {
        $dto = AuthorDTO::fromRequest($request->validated());
        $updatedAuthor = $this->authorService->updateAuthor($author, $dto);
        $authorDTO = AuthorDTO::fromModel($updatedAuthor);

        return $this->handleUpdateResponse(
            request: $request,
            data: $authorDTO,
            message: 'Autor atualizado com sucesso!',
            redirectRoute: 'authors.index'
        );
    }

    public function destroy(Author $author): RedirectResponse|JsonResponse
    {
        $this->authorService->deleteAuthor($author);

        return $this->handleDestroyResponse(
            request: request(),
            message: 'Autor excluído com sucesso!',
            redirectRoute: 'authors.index'
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
