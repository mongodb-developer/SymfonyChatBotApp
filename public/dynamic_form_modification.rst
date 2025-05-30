How to Dynamically Modify Forms Using Form Events
=================================================

Oftentimes, a form can't be created statically. In this article, you'll learn
how to customize your form based on three common use-cases:

1) :ref:`Customizing your Form Based on the Underlying Data <form-events-underlying-data>`

   Example: you have a "Product" form and need to modify/add/remove a field
   based on the data on the underlying Product being edited.

2) :ref:`How to Dynamically Generate Forms Based on User Data <form-events-user-data>`

   Example: you create a "Friend Message" form and need to build a drop-down
   that contains only users that are friends with the *current* authenticated
   user.

3) :ref:`Dynamic Generation for Submitted Forms <form-events-submitted-data>`

   Example: on a registration form, you have a "country" field and a "state"
   field which should populate dynamically based on the value in the "country"
   field.

If you wish to learn more about the basics behind form events, you can
take a look at the :doc:`Form Events </form/events>` documentation.

.. _form-events-underlying-data:

Customizing your Form Based on the Underlying Data
--------------------------------------------------

Before starting with dynamic form generation, remember what
a bare form class looks like::

    // src/Form/Type/ProductType.php
    namespace App\Form\Type;

    use App\Entity\Product;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('name');
            $builder->add('price');
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Product::class,
            ]);
        }
    }

.. note::

    If this particular section of code isn't already familiar to you, you
    probably need to take a step back and first review the :doc:`Forms article </forms>`
    before proceeding.

Assume for a moment that this form utilizes an imaginary "Product" class
that has only two properties ("name" and "price"). The form generated from
this class will look the exact same regardless if a new Product is being created
or if an existing product is being edited (e.g. a product fetched from the database).

Suppose now, that you don't want the user to be able to change the ``name`` value
once the object has been created. To do this, you can rely on Symfony's
:doc:`EventDispatcher component </components/event_dispatcher>`
system to analyze the data on the object and modify the form based on the
Product object's data. In this article, you'll learn how to add this level of
flexibility to your forms.

Adding an Event Listener to a Form Class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

So, instead of directly adding that ``name`` widget, the responsibility of
creating that particular field is delegated to an event listener::

    // src/Form/Type/ProductType.php
    namespace App\Form\Type;

    // ...
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;

    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('price');

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                // ... adding the name field if needed
            });
        }

        // ...
    }

The goal is to create a ``name`` field *only* if the underlying ``Product``
object is new (e.g. hasn't been persisted to the database). Based on that,
the event listener might look like the following::

    // ...
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ...
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $product = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$product || null === $product->getId()) {
                $form->add('name', TextType::class);
            }
        });
    }

.. note::

    The ``FormEvents::PRE_SET_DATA`` line actually resolves to the string
    ``form.pre_set_data``. :class:`Symfony\\Component\\Form\\FormEvents`
    serves an organizational purpose. It is a centralized location in which
    you can find all of the various form events available. You can view the
    full list of form events via the
    :class:`Symfony\\Component\\Form\\FormEvents` class.

Adding an Event Subscriber to a Form Class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For better reusability or if there is some heavy logic in your event listener,
you can also move the logic for creating the ``name`` field to an
:ref:`event subscriber <event_dispatcher-using-event-subscribers>`::

    // src/Form/EventSubscriber/AddNameFieldSubscriber.php
    namespace App\Form\EventSubscriber;

    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;

    class AddNameFieldSubscriber implements EventSubscriberInterface
    {
        public static function getSubscribedEvents(): array
        {
            // Tells the dispatcher that you want to listen on the form.pre_set_data
            // event and that the preSetData method should be called.
            return [FormEvents::PRE_SET_DATA => 'preSetData'];
        }

        public function preSetData(FormEvent $event): void
        {
            $product = $event->getData();
            $form = $event->getForm();

            if (!$product || null === $product->getId()) {
                $form->add('name', TextType::class);
            }
        }
    }

Great! Now use that in your form class::

    // src/Form/Type/ProductType.php
    namespace App\Form\Type;

    // ...
    use App\Form\EventSubscriber\AddNameFieldSubscriber;

    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('price');

            $builder->addEventSubscriber(new AddNameFieldSubscriber());
        }

        // ...
    }

.. _form-events-user-data:

How to Dynamically Generate Forms Based on User Data
----------------------------------------------------

Sometimes you want a form to be generated dynamically based not only on data
from the form but also on something else - like some data from the current user.
Suppose you have a social website where a user can only message people marked
as friends on the website. In this case, a "choice list" of whom to message
should only contain users that are the current user's friends.

Creating the Form Type
~~~~~~~~~~~~~~~~~~~~~~

Using an event listener, your form might look like this::

    // src/Form/Type/FriendMessageFormType.php
    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;

    class FriendMessageFormType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('subject', TextType::class)
                ->add('body', TextareaType::class)
            ;
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                // ... add a choice list of friends of the current application user
            });
        }
    }

The problem is now to get the current user and create a choice field that
contains only this user's friends. This can be done by injecting the ``Security``
service into the form type so you can get the current user object::

    use Symfony\Bundle\SecurityBundle\Security;
    // ...

    class FriendMessageFormType extends AbstractType
    {
        public function __construct(
            private Security $security,
        ) {
        }

        // ....
    }

Customizing the Form Type
~~~~~~~~~~~~~~~~~~~~~~~~~

Now that you have all the basics in place you can use the features of the
security helper to fill in the listener logic::

    // src/Form/Type/FriendMessageFormType.php
    namespace App\Form\Type;

    use App\Entity\User;
    use Doctrine\ORM\EntityRepository;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    // ...

    class FriendMessageFormType extends AbstractType
    {
        public function __construct(
            private Security $security,
        ) {
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('subject', TextType::class)
                ->add('body', TextareaType::class)
            ;

            // grab the user, do a quick sanity check that one exists
            $user = $this->security->getUser();
            if (!$user) {
                throw new \LogicException(
                    'The FriendMessageFormType cannot be used without an authenticated user!'
                );
            }

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user): void {
                if (null !== $event->getData()->getFriend()) {
                    // we don't need to add the friend field because
                    // the message will be addressed to a fixed friend
                    return;
                }

                $form = $event->getForm();

                $formOptions = [
                    'class' => User::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function (UserRepository $userRepository) use ($user): void {
                        // call a method on your repository that returns the query builder
                        // return $userRepository->createFriendsQueryBuilder($user);
                    },
                ];

                // create the field, this is similar the $builder->add()
                // field name, field type, field options
                $form->add('friend', EntityType::class, $formOptions);
            });
        }

        // ...
    }

.. note::

    You might wonder, now that you have access to the ``User`` object, why not
    just use it directly in ``buildForm()`` and omit the event listener? This is
    because doing so in the ``buildForm()`` method would result in the whole
    form type being modified and not just this one form instance. This may not
    usually be a problem, but technically a single form type could be used on a
    single request to create many forms or fields.

Using the Form
~~~~~~~~~~~~~~

If you're using the :ref:`default services.yaml configuration <service-container-services-load-example>`,
your form is ready to be used thanks to :ref:`autowire <services-autowire>` and
:ref:`autoconfigure <services-autoconfigure>`.
Otherwise, :ref:`register the form class as a service <service-container-creating-service>`
and :doc:`tag it </service_container/tags>` with the ``form.type`` tag.

In a controller, create the form like normal::

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class FriendMessageController extends AbstractController
    {
        public function new(Request $request): Response
        {
            $form = $this->createForm(FriendMessageFormType::class);

            // ...
        }
    }

You can also  embed the form type into another form::

    // inside some other "form type" class
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', FriendMessageFormType::class);
    }

.. _form-events-submitted-data:

Dynamic Generation for Submitted Forms
--------------------------------------

Another case that can appear is that you want to customize the form specific to
the data that was submitted by the user. For example, imagine you have a registration
form for sports gatherings. Some events will allow you to specify your preferred
position on the field. This would be a ``choice`` field for example. However, the
possible choices will depend on each sport. Football will have attack, defense,
goalkeeper etc... Baseball will have a pitcher but will not have a goalkeeper. You
will need the correct options in order for validation to pass.

The meetup is passed as an entity field to the form. So we can access each
sport like this::

    // src/Form/Type/SportMeetupType.php
    namespace App\Form\Type;

    use App\Entity\Position;
    use App\Entity\Sport;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    // ...

    class SportMeetupType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('sport', EntityType::class, [
                    'class' => Sport::class,
                    'placeholder' => '',
                ])
            ;

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event): void {
                    $form = $event->getForm();

                    // this would be your entity, i.e. SportMeetup
                    $data = $event->getData();

                    $sport = $data->getSport();
                    $positions = null === $sport ? [] : $sport->getAvailablePositions();

                    $form->add('position', EntityType::class, [
                        'class' => Position::class,
                        'placeholder' => '',
                        'choices' => $positions,
                    ]);
                }
            );
        }

        // ...
    }

When you're building this form to display to the user for the first time,
then this example works perfectly.

However, things get more difficult when you handle the form submission. This
is because the ``PRE_SET_DATA`` event tells us the data that you're starting
with (e.g. an empty ``SportMeetup`` object), *not* the submitted data.

On a form, we can usually listen to the following events:

* ``PRE_SET_DATA``
* ``POST_SET_DATA``
* ``PRE_SUBMIT``
* ``SUBMIT``
* ``POST_SUBMIT``

The key is to add a ``POST_SUBMIT`` listener to the field that your new field
depends on. If you add a ``POST_SUBMIT`` listener to a form child (e.g. ``sport``),
and add new children to the parent form, the Form component will detect the
new field automatically and map it to the submitted client data.

The type would now look like::

    // src/Form/Type/SportMeetupType.php
    namespace App\Form\Type;

    use App\Entity\Position;
    use App\Entity\Sport;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\FormInterface;
    // ...

    class SportMeetupType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('sport', EntityType::class, [
                    'class' => Sport::class,
                    'placeholder' => '',
                ])
            ;

            $formModifier = function (FormInterface $form, ?Sport $sport = null): void {
                $positions = null === $sport ? [] : $sport->getAvailablePositions();

                $form->add('position', EntityType::class, [
                    'class' => Position::class,
                    'placeholder' => '',
                    'choices' => $positions,
                ]);
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier): void {
                    // this would be your entity, i.e. SportMeetup
                    $data = $event->getData();

                    $formModifier($event->getForm(), $data->getSport());
                }
            );

            $builder->get('sport')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier): void {
                    // It's important here to fetch $event->getForm()->getData(), as
                    // $event->getData() will get you the client data (that is, the ID)
                    $sport = $event->getForm()->getData();

                    // since we've added the listener to the child, we'll have to pass on
                    // the parent to the callback function!
                    $formModifier($event->getForm()->getParent(), $sport);
                }
            );

            // by default, action does not appear in the <form> tag
            // you can set this value by passing the controller route
            $builder->setAction($options['action']);
        }

        // ...
    }

You can see that you need to listen on these two events and have different
callbacks only because in two different scenarios, the data that you can use is
available in different events. Other than that, the listeners always perform
exactly the same things on a given form.

.. tip::

    The ``FormEvents::POST_SUBMIT`` event does not allow modifications to the form
    the listener is bound to, but it allows modifications to its parent.

One piece that is still missing is the client-side updating of your form after
the sport is selected. This should be handled by making an AJAX callback to
your application. Assume that you have a sport meetup creation controller::

    // src/Controller/MeetupController.php
    namespace App\Controller;

    use App\Entity\SportMeetup;
    use App\Form\Type\SportMeetupType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    // ...

    class MeetupController extends AbstractController
    {
        #[Route('/create', name: 'app_meetup_create', methods: ['GET', 'POST'])]
        public function create(Request $request): Response
        {
            $meetup = new SportMeetup();
            $form = $this->createForm(SportMeetupType::class, $meetup, ['action' => $this->generateUrl('app_meetup_create')]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // ... save the meetup, redirect etc.
            }

            return $this->render('meetup/create.html.twig', [
                'form' => $form,
            ]);
        }

        // ...
    }

The associated template uses some JavaScript to update the ``position`` form
field according to the current selection in the ``sport`` field:

.. code-block:: html+twig

    {# templates/meetup/create.html.twig #}
    {{ form_start(form, { attr: { id: 'sport_meetup_form' } }) }}
        {{ form_row(form.sport) }}    {# <select id="meetup_sport" ... #}
        {{ form_row(form.position) }} {# <select id="meetup_position" ... #}
        {# ... #}
    {{ form_end(form) }}

    <script>
        const form = document.getElementById('sport_meetup_form');
        const form_select_sport = document.getElementById('meetup_sport');
        const form_select_position = document.getElementById('meetup_position');

        const updateForm = async (data, url, method) => {
          const req = await fetch(url, {
            method: method,
            body: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'charset': 'utf-8'
            }
          });

          const text = await req.text();

          return text;
        };

        const parseTextToHtml = (text) => {
          const parser = new DOMParser();
          const html = parser.parseFromString(text, 'text/html');

          return html;
        };

        const changeOptions = async (e) => {
          const requestBody = e.target.getAttribute('name') + '=' + e.target.value;
          const updateFormResponse = await updateForm(requestBody, form.getAttribute('action'), form.getAttribute('method'));
          const html = parseTextToHtml(updateFormResponse);

          const new_form_select_position = html.getElementById('meetup_position');
          form_select_position.innerHTML = new_form_select_position.innerHTML;
        };

        form_select_sport.addEventListener('change', (e) => changeOptions(e));
    </script>

The major benefit of submitting the whole form to just extract the updated
``position`` field is that no additional server-side code is needed; all the
code from above to generate the submitted form can be reused.
