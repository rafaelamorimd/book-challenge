<template>
    <div class="author-report">
        <div class="header">
            <h1 class="mb-4 text-center text-2xl font-bold">Relatório de Autores</h1>
            <p class="mb-8 text-center text-gray-600">Data de geração: {{ formatDate(new Date()) }}</p>
        </div>

        <div class="authors-container">
            <div v-for="author in authors" :key="author.id" class="author-card mb-6 rounded-lg border p-4 shadow-sm">
                <h2 class="mb-3 text-xl font-semibold text-gray-800">{{ author.name }}</h2>

                <div v-if="author.books && author.books.length" class="books-section ml-4">
                    <h3 class="mb-2 font-medium text-gray-700">Livros:</h3>
                    <div v-for="book in author.books" :key="book.id" class="book-item mb-3">
                        <div class="book-title font-medium">{{ book.title }}</div>
                        <div v-if="book.subjects && book.subjects.length" class="subjects text-sm text-gray-600 italic">
                            Assuntos: {{ formatSubjects(book.subjects) }}
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-500 italic">Nenhum livro encontrado para este autor.</div>
            </div>
        </div>

        <div class="footer mt-8 text-center text-sm text-gray-500">
            <p>Página {{ currentPage }} de {{ totalPages }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Subject {
    id: number;
    name: string;
}

interface Book {
    id: number;
    title: string;
    subjects: Subject[];
}

interface Author {
    id: number;
    name: string;
    books: Book[];
}

defineProps<{
    authors: Author[];
    currentPage: number;
    totalPages: number;
}>();

const formatDate = (date: Date): string => {
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(date);
};

const formatSubjects = (subjects: Subject[]): string => {
    return subjects.map((subject: Subject) => subject.name).join(', ');
};
</script>

<style scoped>
.author-report {
    font-family:
        'Inter',
        system-ui,
        -apple-system,
        sans-serif;
    padding: 2rem;
    max-width: 100%;
    margin: 0 auto;
}

.header {
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

.author-card {
    background-color: white;
    transition: all 0.2s ease;
}

.author-card:hover {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.book-item {
    padding: 0.5rem;
    border-left: 2px solid #e5e7eb;
    margin-left: 0.5rem;
}

.subjects {
    margin-top: 0.25rem;
}

@media print {
    .author-report {
        padding: 0;
    }

    .author-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>
