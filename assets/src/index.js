/* global lazyLoadScriptsEntries:false */

/**
 * @typedef {{ element_selectors: string; offset: string; script_id?: string; script_src: string; }} Entry
 */

window.addEventListener( 'load', () => {
	if ( ! Array.isArray( lazyLoadScriptsEntries ) || ! lazyLoadScriptsEntries.length ) {
		return;
	}

	/**
	 * Inject script tag to body
	 *
	 * @param {string}  src   Script src.
	 * @param {string}  id    Script ID.
	 * @param {boolean} async Script async attribute.
	 */
	const inject_script = ( src, id = '', async = false ) => {
		const script_tag = document.createElement( 'script' );

		script_tag.src = src;

		if ( async ) {
			script_tag.async = true;
		}

		if ( id ) {
			// eslint-disable-next-line no-undef
			const loaded_event = new CustomEvent( id + '-loaded' );

			/**
			 * Dispatch loaded event for this script.
			 */
			const dispatch_event = () => {
				window.dispatchEvent( loaded_event );
			};

			script_tag.id = id;
			script_tag.onload = dispatch_event;
		}

		document.body.appendChild( script_tag );
	};

	/**
	 * Queue entry for lazy loading
	 *
	 * @param {Entry} entry Entry to add to queue.
	 */
	const queue_entry = entry => {
		const { element_selectors, offset = '0px', script_id, script_src, script_async = false } = entry;
		const selectors = element_selectors.split( ',' ).map( selector => selector.trim() );
		const elements = document.querySelectorAll( selectors );

		if ( ! elements || ! elements.length ) {
			return;
		}

		// eslint-disable-next-line no-undef
		const io_instance = new IntersectionObserver(
			entries => {
				if ( entries.some( element => element.isIntersecting ) ) {
					inject_script( script_src, script_id, script_async );

					io_instance.disconnect();
				}
			},
			{
				rootMargin: `0px 0px ${ offset } 0px`,
			},
		);

		elements.forEach( element => {
			io_instance.observe( element );
		} );
	};

	/**
	 * Inject entry script
	 *
	 * @param {Entry} entry Entry to add to queue.
	 * @return {void}
	 */
	const inject_entry = ( { script_id, script_src, script_async } ) =>
		inject_script( script_src, script_id, script_async );

	const callback = typeof window.IntersectionObserverEntry !== 'undefined' ? queue_entry : inject_entry;

	lazyLoadScriptsEntries.forEach( callback );
} );

/**
 * A proxy to lazily call back a function.
 *
 * @type {*}
 */
window.lazyLoadScriptsCallback = new Proxy( [], {
	/**
	 * Proxy Set Method.
	 *
	 * @param {Array}           target   The global array to hold all the callback functions.
	 * @param {string | symbol} property Property name/symbol.
	 * @param {Array}           callback Array that contains callback function.
	 * @return {boolean} Always return true.
	 */
	set( target, property, callback ) {
		// List of event to observe to run the callback.
		const eventList = [ 'keydown', 'mousemove', 'wheel', 'touchmove', 'touchstart', 'touchend' ];
		const function_to_invoke = callback[ 1 ];
		target[ property ] = callback;

		/**
		 * Callback wrapper.
		 */
		const callbackFn = () => {
			if ( typeof function_to_invoke === 'function' ) {
				function_to_invoke();
			}
			eventList.forEach( function ( event ) {
				window.removeEventListener( event, callbackFn );
			} );
		};

		eventList.forEach( function ( event ) {
			window.addEventListener( event, callbackFn, { passive: true } );
		} );

		return true;
	},
} );
