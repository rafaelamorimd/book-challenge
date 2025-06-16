<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Relatório por Autor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            padding: 10px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        .header-title {
            margin-left: 20px;
        }

        h1 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .author-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        .author-header {
            background-color: #f3f4f6;
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 4px;
        }

        .author-header strong {
            color: #2563eb;
        }

        .subject-list {
            list-style-type: disc;
            margin: 0;
            padding-left: 20px;
        }

        .subject-list li {
            margin: 2px 0;
        }

        .empty-message {
            text-align: center;
            color: #6b7280;
            padding: 16px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Relatório por Autor</h1>
    </div>

    @foreach ($authors as $author)
        <div class="author-section">
            <div class="author-header"></div>
            <strong>#{{ $author['authorId'] }}</strong> - {{ $author['authorName'] }}
        </div>

        @if (!isset($author['books']) || count($author['books']) === 0)
            <p class="empty-message">Nenhum livro encontrado.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Livro</th>
                        <th>Editora</th>
                        <th>Edição</th>
                        <th>Ano Pub.</th>
                        <th>Valor (R$)</th>
                        <th>Assuntos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($author['books'] as $book)
                        <tr>
                            <td>{{ $book['bookId'] }}</td>
                            <td>{{ $book['bookTitle'] }}</td>
                            <td>{{ $book['publisher'] }}</td>
                            <td>{{ $book['edition'] }}</td>
                            <td>{{ $book['publicationYear'] }}</td>
                            <td>{{ $book['amount'] }}</td>
                            <td>
                                <ul class="subject-list">
                                    @foreach ($book['subjects'] as $subject)
                                        <li>{{ $subject }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    @endforeach
</body>

</html>
