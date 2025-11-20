# ADR 001: Utilisation du Strategy Pattern pour le système de tarification

## Statut
✅ Accepté - 20 Novembre 2024

## Contexte

### Problème métier

Le système actuel de tarification ShopFlow utilise une série de conditions `if/elseif` pour appliquer les réductions. Cette approche n'est pas scalable et pose plusieurs problèmes business :

- **Rigidité** : Chaque nouvelle promotion nécessite une modification du code et un déploiement
- **Time-to-market** : Le marketing doit attendre les développeurs pour lancer une promotion
- **Risques** : Modifier le code de tarification peut introduire des bugs sur les promotions existantes
- **Opportunités manquées** : Impossible de tester rapidement des promotions flash (24h)

Les promotions peuvent survenir à n'importe quel moment (Black Friday, événements spéciaux, opérations flash) et avec des durées variables. L'équipe marketing a besoin d'autonomie et de rapidité.

### Problèmes techniques

D'un point de vue technique, le code actuel viole plusieurs principes SOLID :

- **Single Responsibility violé** : Le service `OrderService` a 3 responsabilités distinctes :
    1. Orchestrer le calcul du prix
    2. Décider quelle réduction appliquer (logique de décision)
    3. Calculer chaque type de réduction (logique métier)

- **Open/Closed violé** : Chaque nouvelle réduction nécessite de modifier la méthode `calculateTotal()`, augmentant le risque de régression

- **Complexité cyclomatique élevée** : Avec 5 types de réductions, on a 5+ chemins possibles, rendant le code difficile à tester et à maintenir

- **Testabilité** : Impossible de tester unitairement une seule logique de réduction sans setup complexe de toutes les conditions

---

## Décision

Nous avons décidé d'utiliser le **Strategy Pattern** pour encapsuler chaque algorithme de tarification dans sa propre classe indépendante.

### Pourquoi Strategy ?

Le Strategy Pattern résout nos problèmes car :

1. **Encapsulation** : Chaque stratégie de réduction devient une classe avec UNE seule responsabilité (son algorithme)

2. **Extensibilité** : Ajouter une nouvelle réduction = créer une nouvelle classe. Aucun impact sur l'existant, respect d'Open/Closed

3. **Testabilité** : Chaque stratégie est testable unitairement en isolation, sans dépendances complexes

4. **Interchangeabilité** : Toutes les stratégies implémentent la même interface, elles sont substituables (Liskov)

5. **Configuration runtime** : Les stratégies peuvent être sélectionnées et combinées dynamiquement selon les besoins

---

## Conséquences

### Positives ✅

1. **Respect de SOLID** :
    - **Single Responsibility** : Chaque stratégie a UNE seule raison de changer (son algorithme de calcul)
    - **Open/Closed** : Pour ajouter une réduction, on crée une nouvelle classe sans modifier l'existant
    - **Liskov Substitution** : Toutes les stratégies sont interchangeables via l'interface commune
    - **Interface Segregation** : Interface minimaliste avec une seule méthode `calculate()`
    - **Dependency Inversion** : Le Use Case dépend de l'abstraction `PricingStrategyInterface`, pas des implémentations concrètes

2. **Testabilité** :
    - Chaque stratégie est testable unitairement en isolation avec des tests simples
    - Le Use Case peut être testé avec des stratégies mockées
    - Pas besoin de setup complexe pour tester un cas précis (pas de conditions imbriquées)
    - Couverture de code facilitée (chaque stratégie = petit fichier focalisé)

3. **Extensibilité** :
    - Ajout d'une nouvelle stratégie statique en 5 minutes (créer classe + mapping)
    - Ajout d'une stratégie événementielle sans code PHP (juste BDD)
    - Pas de modification du code existant = zéro risque de régression
    - Les nouvelles stratégies sont détectées automatiquement via Symfony service tagging

4. **Maintenabilité** :
    - Code lisible avec des classes petites (15-20 lignes) et focalisées
    - Facile de retrouver la logique d'une réduction spécifique (1 classe = 1 stratégie)
    - Changement d'un algorithme isolé sans impact sur les autres
    - Nouvelle recrue peut comprendre rapidement (pattern standard)

5. **Réutilisabilité** :
    - Les stratégies peuvent être réutilisées dans d'autres contextes (devis, factures, abonnements)
    - Le pattern peut servir de base pour d'autres systèmes de règles métier (taxes, frais de port, commissions)
    - Code DRY : la logique VIP n'existe qu'à un seul endroit

### Négatives ⚠️

1. **Complexité ajoutée** :
    - Introduction de plusieurs classes au lieu d'une seule méthode
    - Nécessite de comprendre le pattern Strategy pour maintenir le code
    - Le `StrategyRegistry` ajoute une couche d'indirection pour résoudre les codes

2. **Nombre de classes** :
    - Passage de 1 classe à 8+ classes (5 stratégies + registry + use case + DTOs)
    - Peut sembler "over-engineering" pour un projet avec seulement 2-3 réductions
    - Plus de fichiers à naviguer dans l'IDE
    - Structure de dossiers plus profonde (4 couches Clean Architecture)

3. **Courbe d'apprentissage** :
    - Les développeurs juniors doivent comprendre le pattern (formation nécessaire)
    - Nécessite une documentation claire de l'architecture (ADR, README)
    - Temps d'onboarding légèrement plus long (1-2 jours pour comprendre la structure)
    - Requiert discipline : ne pas contourner le pattern avec des raccourcis

---

## Alternatives considérées

### Alternative 1 : Garder les if/else avec refactoring

**Description :**
Refactoriser le code actuel en extrayant des méthodes privées pour chaque type de réduction (`calculateVipDiscount()`, `calculateStudentDiscount()`, etc.) tout en gardant la logique de décision dans le service principal.

**Pourquoi rejetée :**

Cette approche ne résout pas le problème fondamental :
- Le `OrderService` aurait toujours 3+ responsabilités (orchestration + décision + calcul)
- **Open/Closed reste violé** : chaque nouvelle réduction = modification du service
- Difficulté à tester : il faut mocker toutes les conditions à chaque test
- Complexité cyclomatique toujours élevée (15+ chemins possibles)
- Impossible d'activer/désactiver des stratégies dynamiquement
- Pas de réutilisabilité (méthodes privées non réutilisables ailleurs)

Même avec des méthodes privées extraites, on reste avec un monolithe difficile à maintenir et à faire évoluer.

### Alternative 2 : Chain of Responsibility

**Description :**
Utiliser une chaîne de responsabilité où chaque handler décide s'il traite la requête ou la passe au suivant. Chaque réduction serait un handler qui vérifie les conditions puis passe au suivant.

**Pourquoi rejetée :**

Chain of Responsibility est adapté quand on veut qu'un handler décide s'il traite la requête OU la passe au suivant (ex: système de validation, pipeline de logging, gestion d'événements).

Dans notre cas, **Strategy est préférable** car :
- On veut appliquer **TOUTES** les stratégies sélectionnées, pas s'arrêter à la première qui match
- L'ordre d'application est **explicite et contrôlé** par le Use Case via le tableau de codes, pas par les handlers eux-mêmes
- Strategy est plus simple : pas de logique de "chaînage" ni de gestion du `next()`
- Les stratégies sont totalement **découplées** les unes des autres (une stratégie ne connaît pas les autres)
- Plus facile à tester : pas besoin de mocker toute la chaîne
- La sélection des stratégies vient du frontend (contrôle métier), pas d'une logique interne

### Alternative 3 : Discount Coupons (système de codes promo)

**Description :**
Créer un système de coupons avec des codes que les clients saisissent manuellement au moment du paiement. Chaque code promo correspondrait à une réduction spécifique.

**Pourquoi rejetée :**

Cette approche ne couvre pas tous nos cas d'usage métier :
- Les réductions VIP/Student sont **automatiques** (basées sur le profil), pas basées sur un code promo saisi
- Elle nécessite une **action utilisateur** (saisir un code), ce qui réduit statistiquement les conversions (friction)
- Ne permet pas de **cumuler facilement** plusieurs types de réductions (VIP + événement)
- Complexité ajoutée pour gérer : expiration des codes, unicité, limites d'utilisation, génération de codes
- Pas adapté aux réductions événementielles automatiques (Black Friday s'applique à tous sans code)
- Risque de fraude (partage de codes, bots)

Les codes promo restent un complément possible (marketing ciblé), mais pas un remplacement de notre système de tarification automatique.

---

## Implémentation technique

### Architecture choisie

Nous avons structuré le code selon **Clean Architecture** en 4 couches strictes :

**Domain** (couche métier pure) :
- Interface `PricingStrategyInterface` : contrat que toutes les stratégies doivent respecter
- Entités métier : `Order`, `Customer`, `Product`, `PromotionalEvent`, `OrderItem`
- Value Object : `CustomerType` (enum)
- Interfaces de repositories (pas d'implémentation)
- **Aucune dépendance** vers Symfony, Doctrine, ou les couches externes

**Application** (cas d'usage) :
- Use Case `CalculateOrderPriceHandler` : orchestre le calcul en utilisant les stratégies
- DTO Input : `CalculateOrderPriceCommand` (customerId, items, strategyCodes)
- DTO Output : `CalculateOrderPriceResponse` (subtotal, total, appliedStrategies)
- Pas de logique de présentation ni d'accès direct aux données

**Infrastructure** (implémentations techniques) :
- Stratégies concrètes : `VipPricingStrategy`, `StudentPricingStrategy`, `EventBasedPricingStrategy`, etc.
- `StrategyRegistry` : résout les codes string ("vip") vers les instances de stratégies
- Repositories Doctrine : implémentent les interfaces du Domain
- Configuration Symfony : service tagging, autowiring

**Presentation** (points d'entrée) :
- `OrderPricingController` : API REST qui reçoit les requêtes HTTP
- Transforme Request → Command
- Appelle le Use Case
- Transforme Response → JSON

### Gestion du cumul de réductions

Pour permettre le cumul de réductions (ex: VIP + Black Friday), nous appliquons les stratégies **successivement** :

**Flux d'exécution :**
1. Le Use Case reçoit un tableau ordonné de codes : `["vip", "black-friday"]`
2. Pour chaque code, il demande la stratégie correspondante au `StrategyRegistry`
3. Il applique chaque stratégie sur le montant résultant de la stratégie précédente
4. Il collecte les résultats intermédiaires pour la réponse

**Exemple concret :**
```
Prix initial : 1000€

Application VIP (-15%) : 
  1000€ × 0.85 = 850€

Application Black Friday (-25%) sur le résultat précédent :
  850€ × 0.75 = 637.50€

Prix final : 637.50€
```

**Le calcul est successif et NON additif** car :
- C'est le comportement métier attendu (réductions en cascade)
- Évite les réductions supérieures à 100% (VIP 15% + BF 25% = 40% additif vs 36.25% successif)
- Permet de contrôler l'ordre d'application si la priorité importe
- Correspond à la réalité commerciale (on applique une remise sur un prix déjà réduit)

### Stratégies statiques vs événementielles

Nous avons identifié 2 types de stratégies avec des besoins différents :

**1. Stratégies statiques** (VIP, Student, Standard) :
- **Logique fixe** codée en dur dans la classe
- **Pas de dépendances externes** (pas de BDD, pas de services)
- **Stateless** : pas de constructeur, méthode `calculate()` pure
- Exemple : `VipPricingStrategy` fait toujours `-15%`

**2. Stratégies événementielles** (Black Friday, Summer Sale) :
- **Logique configurable** via l'entité `PromotionalEvent` stockée en BDD
- **Injection de dépendances** : l'événement et la date actuelle via constructeur
- **Vérification des dates** : la stratégie vérifie si l'événement est actif (`startDate ≤ now ≤ endDate`)
- Même classe `EventBasedPricingStrategy` réutilisée pour tous les événements

Cette distinction permet :
- De ne pas sur-complexifier les stratégies simples
- D'avoir une flexibilité totale sur les événements (créés dynamiquement en BDD)
- De maintenir le principe YAGNI (You Aren't Gonna Need It)

### Extensibilité

**Pour ajouter une nouvelle réduction statique :**
```
1. Créer src/Infrastructure/Strategy/XxxPricingStrategy.php
2. Implémenter PricingStrategyInterface avec calculate(float): float
3. Ajouter le tag Symfony dans config/services.yaml :
   tags: [{ name: 'pricing.strategy', code: 'xxx' }]
4. Créer le test unitaire
Temps estimé : 5-10 minutes
```

**Pour ajouter une nouvelle réduction événementielle :**
```
1. INSERT INTO promotional_event (code, discount_percentage, start_date, end_date)
2. Le système utilise automatiquement EventBasedPricingStrategy
3. Aucun code PHP à modifier, aucun déploiement !
Temps estimé : 2 minutes
```

---

## Impact sur les tests

### Avant (if/else)
```php
// Il fallait tester TOUS les chemins dans une seule méthode
public function test_calculate_total_with_vip_and_black_friday_and_student() {
    // Setup complexe de toutes les conditions
    // Test de 1 chemin parmi 15+
}
```
- Complexité cyclomatique élevée (15+ chemins à tester)
- Setup complexe pour isoler un cas
- Tests fragiles (modification d'un if casse plusieurs tests)

### Après (Strategy)
```php
// Tests unitaires isolés et simples
public function test_vip_strategy_applies_15_percent_discount() {
    $strategy = new VipPricingStrategy();
    $this->assertEquals(85.0, $strategy->calculate(100.0));
}

public function test_use_case_applies_strategies_successively() {
    $mockStrategy1 = $this->createMock(PricingStrategyInterface::class);
    $mockStrategy2 = $this->createMock(PricingStrategyInterface::class);
    // Test du Use Case avec stratégies mockées
}
```
- Tests unitaires simples et ciblés (1 stratégie = 1 test)
- Le Use Case est testé avec des mocks (pas de dépendances)
- Ajout d'une stratégie = 1 nouveau test isolé, les autres ne changent pas

---

## Métriques de décision

| Métrique | Avant (if/else) | Après (Strategy) | Impact |
|----------|-----------------|------------------|--------|
| Nombre de classes | 1 | 8+ | + Plus de fichiers mais mieux organisés |
| Lignes de code par classe | ~100 | ~20 | + Lisibilité ++, maintenance ++ |
| Complexité cyclomatique | 15+ | 2-3 par classe | + Réduction de 80% |
| Couverture de tests possible | ~40% | >80% | + Qualité ++ |
| Temps pour ajouter une réduction | ~30 min | ~5 min | + Productivité 6x |
| Risque de régression | Élevé | Faible | + Stabilité ++ |
| Principes SOLID violés | 2 (S, O) | 0 | + Architecture propre |

---

## Notes d'implémentation

- Cette décision s'aligne avec notre objectif de **Clean Architecture**
- Le pattern Strategy est un **standard de l'industrie** (Gang of Four, 1994)
- Facilite l'**onboarding** des nouveaux développeurs (pattern largement connu et documenté)
- Peut servir de **base réutilisable** pour d'autres systèmes de règles métier (calcul de taxes, frais de port, commissions vendeurs)
- Compatible avec une **évolution future** vers un moteur de règles métier (rule engine) si nécessaire

---

## Références

- [Design Patterns: Elements of Reusable Object-Oriented Software](https://en.wikipedia.org/wiki/Design_Patterns) - Gang of Four (GoF)
- [Refactoring Guru - Strategy Pattern](https://refactoring.guru/design-patterns/strategy)
- [Martin Fowler - Refactoring](https://martinfowler.com/books/refactoring.html)
- [Symfony Best Practices - Service Container](https://symfony.com/doc/current/service_container.html)
- [Clean Architecture - Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
