<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\SubjectDTO;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;

class SubjectController extends Controller
{
    use HandlesApiResponses;

    private const PER_PAGE = 10;
    private const FILTERS = ['search', 'Descricao', 'CodAs'];

    public function __construct(
        private readonly SubjectService $subjectService
    ) {
    }

    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $filters = $this->getFiltersFromRequest($request);
        $subjects = $this->subjectService->getPaginatedSubjects($filters, self::PER_PAGE)
            ->withQueryString();

        return $this->handleIndexResponse(
            request: $request,
            paginatedData: $subjects,
            filters: $request->only(self::FILTERS),
            view: 'Subjects/Index',
            filterKey: 'subjects'
        );
    }

    public function create(): InertiaResponse
    {
        return $this->handleEditResponse(
            request: request(),
            data: null,
            additionalData: [],
            view: 'Subjects/Form'
        );
    }

    public function store(StoreSubjectRequest $request): RedirectResponse|JsonResponse
    {
        $dto = SubjectDTO::fromRequest($request->validated());
        $subject = $this->subjectService->createSubject($dto);
        $subjectDTO = SubjectDTO::fromModel($subject);

        return $this->handleStoreResponse(
            request: $request,
            data: $subjectDTO,
            message: 'Assunto criado com sucesso!',
            redirectRoute: 'subjects.index',
            status: 201
        );
    }

    public function edit(Subject $subject): InertiaResponse|JsonResponse
    {
        $subject = $this->subjectService->getSubject($subject);
        $subjectDTO = SubjectDTO::fromModel($subject);

        return $this->handleEditResponse(
            request: request(),
            data: $subjectDTO,
            additionalData: [],
            view: 'Subjects/Form',
            dataKey: 'subject'
        );
    }

    public function show(Subject $subject): InertiaResponse|JsonResponse
    {
        $subject = $this->subjectService->getSubject($subject);
        $subjectDTO = SubjectDTO::fromRequest($subject->toArray());

        return $this->handleEditResponse(
            request: request(),
            data: $subjectDTO,
            additionalData: [],
            view: 'Subjects/Form',
            dataKey: 'subject'
        );
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse|JsonResponse
    {
        $dto = SubjectDTO::fromRequest($request->validated());
        $updatedSubject = $this->subjectService->updateSubject($subject, $dto);
        $subjectDTO = SubjectDTO::fromModel($updatedSubject);

        return $this->handleUpdateResponse(
            request: $request,
            data: $subjectDTO,
            message: 'Assunto atualizado com sucesso!',
            redirectRoute: 'subjects.index'
        );
    }

    public function destroy(Subject $subject): RedirectResponse|JsonResponse
    {
        $this->subjectService->deleteSubject($subject);

        return $this->handleDestroyResponse(
            request: request(),
            message: 'Assunto excluído com sucesso!',
            redirectRoute: 'subjects.index'
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
