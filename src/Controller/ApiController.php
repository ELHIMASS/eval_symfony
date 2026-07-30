<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/articles', name: 'api_articles')]
    public function articles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllVisible();

        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'title' => $article->getTitle(),
                'slug' => $article->getSlug(),
                'createdAt' => $article->getCreatedAt()->format('Y-m-d'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/article/{slug}', name: 'api_article_item')]
    public function article(Article $article): Response
    {
        if (!$article->isVisible()) {
            throw $this->createNotFoundException();
        }

        $tags = [];
        foreach ($article->getTags() as $tag) {
            $tags[] = $tag->getLabel();
        }

        $data = [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'createdAt' => $article->getCreatedAt()->format('Y-m-d'),
            'category' => $article->getCategory()->getName(),
            'tags' => $tags,
            'url' => $this->generateUrl(
                'article_item',
                ['slug' => $article->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        return $this->json($data);
    }
}
