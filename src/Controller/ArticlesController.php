<?php
// src/Controller/ArticlesController.php
namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function index()
    {
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);
    
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
    
            // Changed: Set the user_id from the current user.
            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();
    
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $tags = $this->Articles->Tags->find('list')->all();
        $this->set(compact('article', 'tags'));
    }

    public function edit($slug)
{
    $article = $this->Articles
        ->findBySlug($slug)
        ->contain('Tags') // load associated Tags
        ->firstOrFail();
    $this->Authorization->authorize($article);

    if ($this->request->is(['post', 'put'])) {
        $this->Articles->patchEntity($article, $this->request->getData(), [
            // Added: Disable modification of user_id.
            'accessibleFields' => ['user_id' => false]
        ]);
        if ($this->Articles->save($article)) {
            $this->Flash->success(__('Your article has been updated.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('Unable to update your article.'));
    }
    $tags = $this->Articles->Tags->find('list')->all();
    $this->set(compact('article', 'tags'));
}

public function delete($slug)
{
    $this->request->allowMethod(['post', 'delete']);

    $article = $this->Articles->findBySlug($slug)->firstOrFail();
    $this->Authorization->authorize($article);
    if ($this->Articles->delete($article)) {
        $this->Flash->success(__('The {0} article has been deleted.', $article->title));
        return $this->redirect(['action' => 'index']);
    }
}

public function tags()
{
    // The 'pass' key is provided by CakePHP and contains all
    // the passed URL path segments in the request.
    $tags = $this->request->getParam('pass');

    // Use the ArticlesTable to find tagged articles.
    $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ])
        ->all();

    // Pass variables into the view template context.
    $this->set([
        'articles' => $articles,
        'tags' => $tags
    ]);
}
}