<?php
declare (strict_types = 1);
namespace MyApp\Controller;

use MyApp\Entity\Avis;
use MyApp\Entity\Cart;
use MyApp\Entity\CartItem;
use MyApp\Entity\Produit;
use MyApp\Entity\Type;
use MyApp\Entity\User;
use MyApp\Model\avisModel;
use MyApp\Model\cartItemModel;
use MyApp\Model\cartModel;
use MyApp\Model\produitModel;
use MyApp\Model\typeModel;
use MyApp\Model\userModel;
use MyApp\Service\DependencyContainer;
use Twig\Environment;

class DefaultController
{
    private $twig;
    private $typeModel;
    private $produitModel;
    private $userModel;
    private $cartModel;
    private $cartItemModel;
    private $avisModel;

    public function __construct(Environment $twig, DependencyContainer $dependencyContainer)
    {
        $this->twig = $twig;
        $this->typeModel = $dependencyContainer->get('TypeModel');
        $this->produitModel = $dependencyContainer->get('ProduitModel');
        $this->userModel = $dependencyContainer->get('UserModel');
        $this->cartModel = $dependencyContainer->get('CartModel');
        $this->cartItemModel = $dependencyContainer->get('CartItemModel');
        $this->avisModel = $dependencyContainer->get('AvisModel');
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(intVal($id), $label);
                $success = $this->typeModel->updateType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }
        $type = $this->typeModel->getOneType(intVal($id));
        echo $this->twig->render('defaultController/updateType.html.twig', ['type' => $type]);
    }

    public function addType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $label = filter_input(INPUT_POST, 'label', FILTER_SANITIZE_STRING);
            if (!empty($_POST['label'])) {
                $type = new Type(null, $label);
                $success = $this->typeModel->createType($type);
                if ($success) {
                    header('Location: index.php?page=types');
                }
            }
        }
        echo $this->twig->render('defaultController/addType.html.twig', []);
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
            $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $roles = filter_input(INPUT_POST, 'roles', FILTER_SANITIZE_STRING);

            $user = new User(intVal($id), $email, $lastname, $firstname, $password, array($roles));
            $success = $this->userModel->updateUser($user);

            if ($success) {
                header('Location: index.php?page=users');
            }
        } else {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user = $this->userModel->getOneUser(intVal($id));
            echo $this->twig->render('defaultController/updateUser.html.twig', ['user' => $user]);
        }
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
            $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];

            $passwordLength = strlen($password);
            $containsDigit = preg_match('/\d/', $password);
            $containsUpper = preg_match('/[A-Z]/', $password);
            $containsLower = preg_match('/[a-z]/', $password);
            $containsSpecial = preg_match('/[^a-zA-Z\d]/', $password);

            if (!$lastname || !$email || !$password || !$firstname) {

                $_SESSION['message'] = 'Erreur : données invalides';
            } elseif ($passwordLength < 12 || !$containsDigit || !$containsUpper || !$containsLower || !$containsSpecial) {
                $_SESSION['message'] = 'Erreur : mot de passe non conforme';
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user = new User(null, $firstname, $lastname, $email, $hashedPassword, ['user']);
                // Enregistrez les données de l'utilisateur dans la base de données
                $result = $this->userModel->createUser($user);

                if ($result) {
                    $_SESSION['message'] = 'Votre inscription est terminée';
                    header('Location: index.php?page=login');
                    exit;
                } else {
                    $_SESSION['message'] = 'Erreur lors de l\'inscription';
                }

            }

            header('Location: index.php?page=addUser');
            exit;
        }

        echo $this->twig->render('defaultController/addUser.html.twig', []);
    }

    public function deleteUser()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->userModel->deleteUser(intVal($id));
        header('Location: index.php?page=users');
    }

    public function logout()
    {
        $_SESSION = array();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                header('Location: index.php?page=login');
            } else {
                if ($user->verifyPassword($password)) {
                    $_SESSION['login'] = $user->getEmail();
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['roles'] = $user->getRoles();
                    if ($_SESSION['roles'] == ["admin"]) {
                        header('Location: index.php?page=homeadmin');
                    } else {
                        header('Location: index.php');
                        exit;
                    }
                } else {
                    $_SESSION['message'] = 'Utilisateur ou mot de passe erroné';
                    header('Location: index.php?page=login');
                    exit;
                }
            }
        }
        echo $this->twig->render('defaultController/login.html.twig', []);
    }

    public function home()
    {
        $avis = $this->avisModel->getAllAvis();
        $produits = $this->produitModel->getAllProduitByhome();
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/home.html.twig', ['produits' => $produits, 'types' => $types, 'avis' => $avis]);
    }

    public function homeadmin()
    {
        $produits = $this->produitModel->getAllProduitByhomeadmin();
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/homeadmin.html.twig', ['produits' => $produits, 'types' => $types]);
    }

    public function contact()
    {
        echo $this->twig->render('defaultController/contact.html.twig', []);
    }

    public function types()
    {
        $types = $this->typeModel->getAllTypes();
        echo $this->twig->render('defaultController/types.html.twig', ['types' => $types]);
    }

    public function produits()
    {
        $idType = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $type = $this->typeModel->getTypeById(intVal($idType));
        if ($type == null) {
            $_SESSION['message'] = 'Le type n\'éxiste pas';
            header('Location: index.php?page=home');
            exit;
        } else {
            $produits = $this->produitModel->getAllProduitByType($type);

        }

        echo $this->twig->render('defaultController/produits.html.twig', ['produits' => $produits, 'type' => $type]);
    }

    public function produitp()
    {
        $id = intVal(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $produit = $this->produitModel->getProduitById($id);
        echo $this->twig->render('defaultController/produitp.html.twig', ['produit' => $produit]);
    }

    public function produits_admin()
    {
        $produits = $this->produitModel->getAllProduits();
        echo $this->twig->render('defaultController/produits_admin.html.twig', ['produits' => $produits]);
    }

    public function addProduit()
    {
        $types = $this->typeModel->getAllTypes();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $descriptions = filter_input(INPUT_POST, 'descriptions', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $idType = filter_input(INPUT_POST, 'idType', FILTER_SANITIZE_NUMBER_INT);
            if (!empty($name) && !empty($descriptions) && !empty($price) && !empty($idType)) {
                $type = $this->typeModel->getTypeById(intVal($idType));
                if ($type == null) {
                    $_SESSION['message'] = 'Erreur sur le type.';
                } else {
                    $produit = new Produit(null, $name, $descriptions, floatVal($price), $type);
                    $success = $this->produitModel->createProduit($produit);
                }
            } else {
                $_SESSION['message'] = 'Veuillez saisir toutes les données.';
            }
        }
        echo $this->twig->render('defaultController/addProduit.html.twig', ['types' =>
            $types]);
    }

    public function updateProduit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $descriptions = filter_input(INPUT_POST, 'descriptions', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
            $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

            $produit = new Produit(intVal($id), $name, $descriptions, $price, $type);
            $success = $this->produitModel->updateProduit($produit);

            if ($success) {
                header('Location: index.php?page=produit');
            } else {
                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            }
            $produit = $this->produitModel->getOneProduit(intVal($id));
            echo $this->twig->render('defaultController/updateProduit.html.twig', ['produit' => $produit]);
        }
    }

    public function updateCart()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $cart = $this->cartModel->getOneCart(intVal($id));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            $cart->setStatus($status);
            $success = $this->cartModel->updateCart($cart);

            if ($success) {
                header('Location: index.php?page=cart');
            } else {
                $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            }

        }
        echo $this->twig->render('defaultController/updateCart.html.twig', ['cart' => $cart]);
    }

    public function deleteProduit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->produitModel->deleteProduit(intVal($id));
        header('Location: index.php?page=produits');
    }

    public function deleteCart()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->cartModel->deleteCart(intVal($id));
        header('Location: index.php?page=cart');
    }

    public function users()
    {
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/users.html.twig', ['users' => $users]);
    }

    public function userperso()
    {
        $userperso = $this->userModel->getOneUser();
        echo $this->twig->render('defaultController/userperso.html.twig', ['userperso' => $userperso]);
    }

    public function cart()
    {
        $user = $this->userModel->getUserByEmail($_SESSION['login']);
        if ($user == null) {
            $_SESSION['message'] = 'Veuillez vous connecter.';
            header('Location: index.php?page=home');
        }
        $userid = $user->getId();
        $carts = $this->cartModel->getCartById($userid);
        echo $this->twig->render('defaultController/cart.html.twig', ['carts' => $carts]);
    }

    public function error404()
    {
        echo $this->twig->render('defaultController/error404.html.twig', []);
    }

    public function error403()
    {
        echo $this->twig->render('defaultController/error403.html.twig', []);
    }

    public function error500()
    {
        echo $this->twig->render('defaultController/error500.html.twig', []);
    }

    public function deleteType()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $this->typeModel->deleteType(intVal($id));
        header('Location: index.php?page=types');
    }

    public function addcart()
    {
        {
            $users = $this->userModel->getAllUsers();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $creationdate = date("Y-m-d H:i:s");
                $status = "En cours";
                $idUser = filter_input(INPUT_POST, 'idUser', FILTER_SANITIZE_NUMBER_INT);
                if (!empty($creationdate) && !empty($status) && !empty($idUser)) {
                    $user = $this->userModel->getUserById(intVal($idUser));
                    if ($user == null) {
                        $_SESSION['message'] = 'Erreur sur le user.';
                    } else {
                        $cart = new Cart(null, $creationdate, $status, $user);
                        $success = $this->cartModel->createCart($cart);
                    }
                } else {
                    $_SESSION['message'] = 'Veuillez saisir toutes les données.';
                }
            }
            echo $this->twig->render('defaultController/addcart.html.twig', ['users' => $users]);
        }
    }

    public function avis()
    {
        $id = intVal(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $avis = $this->avisModel->getAllAvisById($id);
        echo $this->twig->render('defaultController/avis.html.twig', ['avis' => $avis]);
    }

    public function addAvis()
    {
        {
            if ($_SESSION == null) {
                $_SESSION['message'] = 'Veuillez vous connectez.';
                header('Location: index.php?page=login');
            }
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                $numerotation = intVal(filter_input(INPUT_POST, 'numerotation', FILTER_SANITIZE_NUMBER_INT));
                $idProduit = $id;
                if (!empty($description) && !empty($numerotation) && !empty($idProduit)) {
                    $produit = $this->produitModel->getOneProduit(intVal($idProduit));
                    if ($produit == null) {
                        $_SESSION['message'] = 'Erreur sur le produit.';
                    } else {
                        $avis = new Avis(null, $description, $numerotation, $produit);
                        $success = $this->avisModel->createAvis($avis);
                        $_SESSION['message'] = 'Votre avis a bien été pris en compte';
                        header('Location: index.php?page=home');
                        exit;
                    }
                } else {
                    $_SESSION['message'] = 'Veuillez saisir toutes les données.';
                }
            }
            echo $this->twig->render('defaultController/addAvis.html.twig', []);
        }
    }

    public function addcartperso()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $creationdate = date("Y-m-d H:i:s");
            $status = "En cours";
            $userId = $_SESSION['user_id'];
            if (!empty($creationdate) && !empty($status) && !empty($userId)) {
                $user = $this->userModel->getUserById(intVal($userId));
                if ($user == null) {
                    $_SESSION['message'] = 'Erreur sur l\'utilisateur.';
                } else {
                    $cart = new Cart(null, $creationdate, $status, $user);
                    $success = $this->cartModel->createCart($cart);
                    header('Location: index.php?page=cart');
                    exit;
                }
            } else {
                $_SESSION['message'] = 'Veuillez saisir toutes les données.';
            }
        }
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('defaultController/addcartperso.html.twig', ['users' => $users]);
    }

    public function addtocart()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $id = intval($id);
        $user = $this->userModel->getUserByEmail($_SESSION['login']);
        if ($user == null) {
            $_SESSION['message'] = 'Veuillez vous connecter.';
            header('Location: index.php?page=home');
        }
        $userid = $user->getId();
        $carts = $this->cartModel->getCartById($userid);
        $cart = $this->cartModel->getCartById($userid)[0];
        $unit = $this->produitModel->getPriceById($id);
        $produit = $this->produitModel->getProduitById($id);
        $cartitem = new CartItem(null, 1, (string) $unit, $cart, $produit);
        $success = $this->cartItemModel->createCartItem($cartitem);
        echo $this->twig->render('defaultController/addtocart.html.twig', ['carts' => $carts]);
    }

    public function cartitem()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $id = intval($id);
        $user = $this->userModel->getUserByEmail($_SESSION['login']);
        if ($user == null) {
            $_SESSION['message'] = 'Veuillez vous connecter.';
            header('Location: index.php?page=home');
        }
        $cart = $this->cartModel->getCartByIdId($id);
        $cartitems = $this->cartItemModel->getCartItemById($id, $cart);
        echo $this->twig->render('defaultController/cartitem.html.twig', ['cartitems' => $cartitems]);
    }

    public function validation()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $cart = $this->cartModel->getOneCart(intVal($id));

        if ($cart !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            if ($status === 'finished') {
                $cart->setStatus($status);
                $success = $this->cartModel->updateCart($cart);

                if ($success) {
                    header('Location: index.php?page=cart');
                    exit;
                } else {
                    // Gérer l'échec de la mise à jour du panier
                }
            }
        } else {
            // Gérer le cas où le panier n'a pas été trouvé
        }

        echo $this->twig->render('defaultController/validation.html.twig', ['cart' => $cart]);
    }

}
