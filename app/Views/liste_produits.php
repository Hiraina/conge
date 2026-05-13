<h1>Liste des produits</h1>
<ul>
    <?php if (isset($produits) && is_array($produits)): ?>
        <?php foreach ($produits as $produit): ?>
            <li><?= esc($produit['nom']) ?> - <?= esc($produit['prix']) ?> €</li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>Aucun produit trouvé.</li>
    <?php endif; ?>
</ul>

<h2>Ajouter un produit</h2>
<form action="/produit/store" method="post">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>
    <br>
    <label for="prix">Prix :</label>
    <input type="number" step="0.01" name="prix" id="prix" required>
    <br>
    <button type="submit">Ajouter</button>
</form>