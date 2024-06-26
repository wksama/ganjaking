/**
 * https://github.com/bugwheels94/math-expression-evaluator
 * math-expression-evaluator version 1.3.8
 *
 * Renamed methods for compatibility
 * reformatted code by themeComplete
 */

( function( window ) {
	'use strict';

	var TCMexp = function( parsed ) {
		this.value = parsed;
	};

	var token = [
		'round',
		'ceil',
		'floor',
		'abs',
		'exp',
		'sqrt',
		'sin',
		'cos',
		'tan',
		'pi',
		'(',
		')',
		'P',
		'C',
		' ',
		'asin',
		'acos',
		'atan',
		'7',
		'8',
		'9',
		'int',
		'cosh',
		'acosh',
		'ln',
		'^',
		'root',
		'4',
		'5',
		'6',
		'/',
		'!',
		'tanh',
		'atanh',
		'Mod',
		'1',
		'2',
		'3',
		'&&',
		'||',
		'*',
		'sinh',
		'asinh',
		'e',
		'log',
		'0',
		'.',
		'+',
		'-',
		',',
		'Sigma',
		'n',
		'Pi',
		'pow',
		'>',
		'<',
		'>=',
		'<=',
		'==',
		'!=',
		'%',
		'min',
		'max'
	];
	var show = [
		'round',
		'ceil',
		'floor',
		'abs',
		'exp',
		'sqrt',
		'sin',
		'cos',
		'tan',
		'&pi;',

		'(',
		')',
		'P',
		'C',
		' ',
		'asin',
		'acos',
		'atan',
		'7',
		'8',

		'9',
		'Int',
		'cosh',
		'acosh',
		' ln',
		'^',
		'root',
		'4',
		'5',
		'6',

		'&divide;',
		'!',
		'tanh',
		'atanh',
		' Mod ',
		'1',
		'2',
		'3',
		'&amp;&amp;',
		'||',
		'&times;',
		'sinh',
		'asinh',
		'e',
		' log',
		'0',
		'.',
		'+',
		'-',
		',',
		'&Sigma;',
		'n',
		'&Pi;',
		'pow',
		'&gt;',
		'&lt;',
		'&gt;=',
		'&lt;=',
		'==',
		'!=',
		'%',
		'min',
		'max'
	];
	var eva = [];

	var newAr = [
		[],
		[ '1', '2', '3', '7', '8', '9', '4', '5', '6', '+', '-', '*', '/', '(', ')', '^', '!', 'P', 'C', 'e', '0', '.', ',', 'n', ' ', '>', '<', '%' ],
		[ 'pi', 'ln', 'Pi', '&&', '||', '>=', '<=', '==', '!=' ],
		[ 'round', 'ceil', 'floor', 'abs', 'exp', 'sqrt', 'sin', 'cos', 'tan', 'Del', 'int', 'Mod', 'log', 'pow', 'min', 'max' ],
		[ 'asin', 'acos', 'atan', 'cosh', 'root', 'tanh', 'sinh' ],
		[ 'acosh', 'atanh', 'asinh', 'Sigma' ]
	];
	var type = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 4, 5, 10, 10, 14, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 10, 0, 1, 1, 1, 2, 7, 0, 0, 2, 1, 1, 1, 9, 0, 2, 0, 0, 3, 0, 1, 6, 9, 9, 11, 12, 13, 12, 8, 2, 2, 2, 2, 2, 2, 9, 8, 8 ];
	/*
     0 : function with syntax function_name(Maths_exp)
     1 : numbers
     2 : binary operators like * / Mod left associate and same precedence
     3 : Math constant values like e,pi,Cruncher ans
     4 : opening bracket
     5 : closing bracket
     6 : decimal
     7 : function with syntax (Math_exp)function_name
     8 : function with syntax function_name(Math_exp1,Math_exp2)
     9 : binary operator like +,-
     10: binary operator like P C or ^
     11: ,
     12: function with , seperated three parameters and third parameter is a string that will be TCMexp string
     13: variable of Sigma function
     */
	var preced = {
		0: 11,
		1: 0,
		2: 3,
		3: 0,
		4: 0,
		5: 0,
		6: 0,
		7: 11,
		8: 11,
		9: 1,
		10: 10,
		11: 0,
		12: 11,
		13: 0,
		14: -1 // will be filtered after lexer
	};
	var type0 = {
		0: true,
		1: true,
		3: true,
		4: true,
		6: true,
		8: true,
		9: true,
		12: true,
		13: true,
		14: true
	}; // type2:true,type4:true,type9:true,type11:true,type21:true,type22
	var type1 = {
		0: true,
		1: true,
		2: true,
		3: true,
		4: true,
		5: true,
		6: true,
		7: true,
		8: true,
		9: true,
		10: true,
		11: true,
		12: true,
		13: true
	}; // type3:true,type5:true,type7:true,type23
	var type1Asterick = {
		0: true,
		3: true,
		4: true,
		8: true,
		12: true,
		13: true
	};
	var empty = {};
	var type3Asterick = {
		0: true,
		1: true,
		3: true,
		4: true,
		6: true,
		8: true,
		12: true,
		13: true
	}; // type_5:true,type_7:true,type_23
	var type6 = {
		1: true
	};

	function inc( arr, val ) {
		var i;
		for ( i = 0; i < arr.length; i++ ) {
			arr[ i ] += val;
		}
		return arr;
	}

	function match( str1, str2, i, x ) {
		var f;
		for ( f = 0; f < x; f++ ) {
			if ( str1[ i + f ] !== str2[ f ] ) {
				return false;
			}
		}
		return true;
	}

	TCMexp.math = {
		isDegree: false, // mode of calculator
		acos: function( x ) {
			return TCMexp.math.isDegree ? ( 180 / Math.PI ) * Math.acos( x ) : Math.acos( x );
		},
		add: function( a, b ) {
			return a + b;
		},
		asin: function( x ) {
			return TCMexp.math.isDegree ? ( 180 / Math.PI ) * Math.asin( x ) : Math.asin( x );
		},
		atan: function( x ) {
			return TCMexp.math.isDegree ? ( 180 / Math.PI ) * Math.atan( x ) : Math.atan( x );
		},
		acosh: function( x ) {
			return Math.log( x + Math.sqrt( ( x * x ) - 1 ) );
		},
		asinh: function( x ) {
			return Math.log( x + Math.sqrt( ( x * x ) + 1 ) );
		},
		atanh: function( x ) {
			return Math.log( ( 1 + x ) / ( 1 - x ) );
		},
		C: function( n, r ) {
			var pro = 1;
			var other = n - r;
			var choice = r;
			var i;
			if ( choice < other ) {
				choice = other;
				other = r;
			}
			for ( i = choice + 1; i <= n; i++ ) {
				pro *= i;
			}
			return pro / TCMexp.math.fact( other );
		},
		changeSign: function( x ) {
			return -x;
		},
		cos: function( x ) {
			if ( TCMexp.math.isDegree ) {
				x = TCMexp.math.toRadian( x );
			}
			return Math.cos( x );
		},
		cosh: function( x ) {
			return ( Math.pow( Math.E, x ) + Math.pow( Math.E, -1 * x ) ) / 2;
		},
		div: function( a, b ) {
			return a / b;
		},
		fact: function( n ) {
			var pro;
			var i;
			if ( n % 1 !== 0 ) {
				return 'NaN';
			}
			pro = 1;
			for ( i = 2; i <= n; i++ ) {
				pro *= i;
			}
			return pro;
		},
		inverse: function( x ) {
			return 1 / x;
		},
		log: function( i ) {
			return Math.log( i ) / Math.log( 10 );
		},
		mod: function( a, b ) {
			return a % b;
		},
		mul: function( a, b ) {
			return a * b;
		},
		P: function( n, r ) {
			var pro = 1;
			var i;
			for ( i = Math.floor( n ) - Math.floor( r ) + 1; i <= Math.floor( n ); i++ ) {
				pro *= i;
			}
			return pro;
		},
		Pi: function( low, high, ex ) {
			var pro = 1;
			var i;
			for ( i = low; i <= high; i++ ) {
				pro *= Number(
					ex.postfixEval( {
						n: i
					} )
				);
			}
			return pro;
		},
		pow10x: function( e ) {
			var x = 1;
			while ( e-- ) {
				x *= 10;
			}
			return x;
		},
		sigma: function( low, high, ex ) {
			var sum = 0;
			var i;
			for ( i = low; i <= high; i++ ) {
				sum += Number(
					ex.postfixEval( {
						n: i
					} )
				);
			}
			return sum;
		},
		sin: function( x ) {
			if ( TCMexp.math.isDegree ) {
				x = TCMexp.math.toRadian( x );
			}
			return Math.sin( x );
		},
		sinh: function( x ) {
			return ( Math.pow( Math.E, x ) - Math.pow( Math.E, -1 * x ) ) / 2;
		},
		sub: function( a, b ) {
			return a - b;
		},
		tan: function( x ) {
			if ( TCMexp.math.isDegree ) {
				x = TCMexp.math.toRadian( x );
			}
			return Math.tan( x );
		},
		tanh: function( x ) {
			return TCMexp.sinha( x ) / TCMexp.cosha( x );
		},
		toRadian: function( x ) {
			return ( x * Math.PI ) / 180;
		},
		greaterthan: function( a, b ) {
			return a > b ? 1 : 0;
		},
		lessthan: function( a, b ) {
			return a < b ? 1 : 0;
		},
		greaterthanorequal: function( a, b ) {
			return a >= b ? 1 : 0;
		},
		lessthanorequal: function( a, b ) {
			return a <= b ? 1 : 0;
		},
		equals: function( a, b ) {
			return String( a ) === String( b ) ? 1 : 0;
		},
		noteequals: function( a, b ) {
			return String( a ) !== String( b ) ? 1 : 0;
		},
		and: function( a, b ) {
			return a && b ? 1 : 0;
		},
		or: function( a, b ) {
			return a || b ? 1 : 0;
		},
		min: function() {
			var array = Array.prototype.slice.call( arguments );
			var result;
			if ( arguments.length === 0 ) {
				result = 0;
			} else {
				result = Math.min.apply( null, array );
				if ( isNaN( result ) ) {
					result = 0;
				}
			}
			return result;
		},
		max: function() {
			var array = Array.prototype.slice.call( arguments );
			var result;
			if ( arguments.length === 0 ) {
				result = 0;
			} else {
				result = Math.max.apply( null, array );
				if ( isNaN( result ) ) {
					result = 0;
				}
			}
			return result;
		}
	};

	eva = [
		Math.round,
		Math.ceil,
		Math.floor,
		Math.abs,
		Math.exp,
		Math.sqrt,
		TCMexp.math.sin,
		TCMexp.math.cos,
		TCMexp.math.tan,
		'PI',
		'(',
		')',
		TCMexp.math.P,
		TCMexp.math.C,
		' '.anchor,
		TCMexp.math.asin,
		TCMexp.math.acos,
		TCMexp.math.atan,
		'7',
		'8',
		'9',
		Math.floor,
		TCMexp.math.cosh,
		TCMexp.math.acosh,
		Math.log,
		Math.pow,
		Math.sqrt,
		'4',
		'5',
		'6',
		TCMexp.math.div,
		TCMexp.math.fact,
		TCMexp.math.tanh,
		TCMexp.math.atanh,
		TCMexp.math.mod,
		'1',
		'2',
		'3',
		TCMexp.math.and,
		TCMexp.math.or,
		TCMexp.math.mul,
		TCMexp.math.sinh,
		TCMexp.math.asinh,
		'E',
		TCMexp.math.log,
		'0',
		'.',
		TCMexp.math.add,
		TCMexp.math.sub,
		',',
		TCMexp.math.sigma,
		'n',
		TCMexp.math.Pi,
		Math.pow,
		TCMexp.math.greaterthan,
		TCMexp.math.lessthan,
		TCMexp.math.greaterthanorequal,
		TCMexp.math.lessthanorequal,
		TCMexp.math.equals,
		TCMexp.math.noteequals,
		TCMexp.math.mod,
		TCMexp.math.min,
		TCMexp.math.max
	];

	TCMexp.prototype.formulaEval = function() {
		var pop1, pop2, pop3;
		var disp = [];
		var arr = this.value;
		var i;
		for ( i = 0; i < arr.length; i++ ) {
			if ( arr[ i ].type === 1 || arr[ i ].type === 3 ) {
				disp.push( { value: arr[ i ].type === 3 ? arr[ i ].show : arr[ i ].value, type: 1 } );
			} else if ( arr[ i ].type === 13 ) {
				disp.push( { value: arr[ i ].show, type: 1 } );
			} else if ( arr[ i ].type === 0 ) {
				disp[ disp.length - 1 ] = {
					value: arr[ i ].show + ( arr[ i ].show !== '-' ? '(' : '' ) + disp[ disp.length - 1 ].value + ( arr[ i ].show !== '-' ? ')' : '' ),
					type: 0
				};
			} else if ( arr[ i ].type === 7 ) {
				disp[ disp.length - 1 ] = {
					value: ( disp[ disp.length - 1 ].type !== 1 ? '(' : '' ) + disp[ disp.length - 1 ].value + ( disp[ disp.length - 1 ].type !== 1 ? ')' : '' ) + arr[ i ].show,
					type: 7
				};
			} else if ( arr[ i ].type === 10 ) {
				pop1 = disp.pop();
				pop2 = disp.pop();
				if ( arr[ i ].show === 'P' || arr[ i ].show === 'C' ) {
					disp.push( {
						value: '<sup>' + pop2.value + '</sup>' + arr[ i ].show + '<sub>' + pop1.value + '</sub>',
						type: 10
					} );
				} else {
					disp.push( {
						value: ( pop2.type !== 1 ? '(' : '' ) + pop2.value + ( pop2.type !== 1 ? ')' : '' ) + '<sup>' + pop1.value + '</sup>',
						type: 1
					} );
				}
			} else if ( arr[ i ].type === 2 || arr[ i ].type === 9 ) {
				pop1 = disp.pop();
				pop2 = disp.pop();
				disp.push( {
					value: ( pop2.type !== 1 ? '(' : '' ) + pop2.value + ( pop2.type !== 1 ? ')' : '' ) + arr[ i ].show + ( pop1.type !== 1 ? '(' : '' ) + pop1.value + ( pop1.type !== 1 ? ')' : '' ),
					type: arr[ i ].type
				} );
			} else if ( arr[ i ].type === 12 ) {
				pop1 = disp.pop();
				pop2 = disp.pop();
				pop3 = disp.pop();
				disp.push( { value: arr[ i ].show + '(' + pop3.value + ',' + pop2.value + ',' + pop1.value + ')', type: 12 } );
			}
		}
		return disp[ 0 ].value;
	};

	TCMexp.addToken = function( tokens ) {
		var i;
		var x;
		var temp;
		var y;
		for ( i = 0; i < tokens.length; i++ ) {
			x = tokens[ i ].token.length;
			temp = -1;

			// newAr is a specially designed data structure index of 1d array = length of tokens
			newAr[ x ] = newAr[ x ] || [];

			for ( y = 0; y < newAr[ x ].length; y++ ) {
				if ( tokens[ i ].token === newAr[ x ][ y ] ) {
					temp = token.indexOf( newAr[ x ][ y ] );
					break;
				}
			}
			if ( temp === -1 ) {
				token.push( tokens[ i ].token );
				type.push( tokens[ i ].type );
				if ( newAr.length <= tokens[ i ].token.length ) {
					newAr[ tokens[ i ].token.length ] = [];
				}
				newAr[ tokens[ i ].token.length ].push( tokens[ i ].token );
				eva.push( tokens[ i ].value );
				show.push( tokens[ i ].show );
			} else { // overwrite
				token[ temp ] = tokens[ i ].token;
				type[ temp ] = tokens[ i ].type;
				eva[ temp ] = tokens[ i ].value;
				show[ temp ] = tokens[ i ].show;
			}
		}
	};

	function tokenize( string ) {
		var nodes = [];
		var length = string.length;
		var key, x, y;
		var i;
		var index;
		for ( i = 0; i < length; i++ ) {
			if ( i < length - 1 && string[ i ] === ' ' && string[ i + 1 ] === ' ' ) {
				continue;
			}
			key = '';
			for ( x = ( string.length - i > ( newAr.length - 2 ) ? newAr.length - 1 : string.length - i ); x > 0; x-- ) {
				if ( newAr[ x ] === undefined ) {
					continue;
				}
				for ( y = 0; y < newAr[ x ].length; y++ ) {
					if ( match( string, newAr[ x ][ y ], i, x ) ) {
						key = newAr[ x ][ y ];
						y = newAr[ x ].length;
						x = 0;
					}
				}
			}
			i += key.length - 1;
			if ( key === '' ) {
				throw ( new TCMexp.Exception( 'Can\'t understand after ' + string.slice( i ) ) );
			}
			index = token.indexOf( key );
			nodes.push( {
				index: index,
				token: key,
				type: type[ index ],
				eval: eva[ index ],
				precedence: preced[ type[ index ] ],
				show: show[ index ]
			} );
		}
		return nodes;
	}

	TCMexp.lex = function( inp, tokens ) {
		var changeSignObj = {
			value: TCMexp.math.changeSign,
			type: 0,
			pre: 21,
			show: '-'
		};
		var closingParObj = {
			value: ')',
			show: ')',
			type: 5,
			pre: 0
		};
		var openingParObj = {
			value: '(',
			type: 4,
			pre: 0,
			show: '('
		};
		var str = [ openingParObj ];
		var ptc = []; // Parenthesis to close at the beginning is after one token
		var inpStr = inp;
		var allowed = type0;
		var bracToClose = 0;
		var asterick = empty;
		var prevKey = '';
		var i;
		var obj = {};
		var nodes = tokenize( inpStr );
		var cToken;
		var cType;
		var cEv;
		var cPre;
		var cShow;
		var pre;
		var j;
		var node;

		if ( typeof tokens !== 'undefined' ) {
			TCMexp.addToken( tokens );
		}

		for ( i = 0; i < nodes.length; i++ ) {
			node = nodes[ i ];

			if ( node.type === 14 ) {
				if ( i > 0 && i < nodes.length - 1 && nodes[ i + 1 ].type === 1 && ( nodes[ i - 1 ].type === 1 || nodes[ i - 1 ].type === 6 ) ) {
					throw new TCMexp.Exception( 'Unexpected Space' );
				}
				continue;
			}

			cToken = node.token;
			cType = node.type;
			cEv = node.eval;
			cPre = node.precedence;
			cShow = node.show;
			pre = str[ str.length - 1 ];

			for ( j = ptc.length; j--; ) {
				// loop over ptc
				if ( ptc[ j ] === 0 ) {
					if ( [ 0, 2, 3, 4, 5, 9, 11, 12, 13 ].indexOf( cType ) !== -1 ) {
						if ( allowed[ cType ] !== true ) {
							throw new TCMexp.Exception( cToken + ' is not allowed after ' + prevKey );
						}
						str.push( closingParObj );
						allowed = type1;
						asterick = type3Asterick;
						inc( ptc, -1 ).pop();
					}
				} else {
					break;
				}
			}
			if ( allowed[ cType ] !== true ) {
				throw new TCMexp.Exception( cToken + ' is not allowed after ' + prevKey );
			}
			if ( asterick[ cType ] === true ) {
				cType = 2;
				cEv = TCMexp.math.mul;
				cShow = '&times;';
				cPre = 3;
				i = i - 1;
			}
			obj = {
				value: cEv,
				type: cType,
				pre: cPre,
				show: cShow
			};
			if ( cType === 0 ) {
				allowed = type0;
				asterick = empty;
				inc( ptc, 2 ).push( 2 );
				str.push( obj );
				str.push( openingParObj );
			} else if ( cType === 1 ) {
				if ( pre.type === 1 ) {
					pre.value += cEv;
					inc( ptc, 1 );
				} else {
					str.push( obj );
				}
				allowed = type1;
				asterick = type1Asterick;
			} else if ( cType === 2 ) {
				allowed = type0;
				asterick = empty;
				inc( ptc, 2 );
				str.push( obj );
			} else if ( cType === 3 ) {
				// constant
				str.push( obj );
				allowed = type1;
				asterick = type3Asterick;
			} else if ( cType === 4 ) {
				inc( ptc, 1 );
				bracToClose++;
				allowed = type0;
				asterick = empty;
				str.push( obj );
			} else if ( cType === 5 ) {
				if ( ! bracToClose ) {
					throw new TCMexp.Exception( 'Closing parenthesis are more than opening one, wait What!!!' );
				}
				bracToClose--;
				allowed = type1;
				asterick = type3Asterick;
				str.push( obj );
				inc( ptc, 1 );
			} else if ( cType === 6 ) {
				if ( pre.hasDec ) {
					throw new TCMexp.Exception( 'Two decimals are not allowed in one number' );
				}
				if ( pre.type !== 1 ) {
					pre = {
						value: 0,
						type: 1,
						pre: 0
					}; // pre needs to be changed as it will the last value now to be safe in later code
					str.push( pre );
					inc( ptc, -1 );
				}
				allowed = type6;
				inc( ptc, 1 );
				asterick = empty;
				pre.value += cEv;
				pre.hasDec = true;
			} else if ( cType === 7 ) {
				allowed = type1;
				asterick = type3Asterick;
				inc( ptc, 1 );
				str.push( obj );
			}
			if ( cType === 8 ) {
				allowed = type0;
				asterick = empty;
				inc( ptc, 4 ).push( 4 );
				str.push( obj );
				str.push( openingParObj );
			} else if ( cType === 9 ) {
				if ( pre.type === 9 ) {
					if ( pre.value === TCMexp.math.add ) {
						pre.value = cEv;
						pre.show = cShow;
						inc( ptc, 1 );
					} else if ( pre.value === TCMexp.math.sub && cShow === '-' ) {
						pre.value = TCMexp.math.add;
						pre.show = '+';
						inc( ptc, 1 );
					}
				} else if ( pre.type !== 5 && pre.type !== 7 && pre.type !== 1 && pre.type !== 3 && pre.type !== 13 ) {
					// changesign only when negative is found
					if ( cToken === '-' ) {
						// do nothing for + token
						// don't add with the above if statement as that will run the else statement of parent if on Ctoken +
						allowed = type0;
						asterick = empty;
						inc( ptc, 2 ).push( 2 );
						str.push( changeSignObj );
						str.push( openingParObj );
					}
				} else {
					str.push( obj );
					inc( ptc, 2 );
				}
				allowed = type0;
				asterick = empty;
			} else if ( cType === 10 ) {
				allowed = type0;
				asterick = empty;
				inc( ptc, 2 );
				str.push( obj );
			} else if ( cType === 11 ) {
				allowed = type0;
				asterick = empty;
				str.push( obj );
			} else if ( cType === 12 ) {
				allowed = type0;
				asterick = empty;
				inc( ptc, 6 ).push( 6 );
				str.push( obj );
				str.push( openingParObj );
			} else if ( cType === 13 ) {
				allowed = type1;
				asterick = type3Asterick;
				str.push( obj );
			}
			inc( ptc, -1 );
			prevKey = cToken;
		}
		for ( j = ptc.length; j--; ) {
			// loop over ptc
			if ( ptc[ j ] === 0 ) {
				str.push( closingParObj );
				inc( ptc, -1 ).pop();
			} else {
				break;
			} // if it is not zero so before ptc also cant be zero
		}
		if ( allowed[ 5 ] !== true ) {
			throw new TCMexp.Exception( 'complete the expression' );
		}
		while ( bracToClose-- ) {
			str.push( closingParObj );
		}

		str.push( closingParObj );

		return new TCMexp( str );
	};

	TCMexp.Exception = function( message ) {
		this.message = message;
	};

	TCMexp.prototype.toPostfix = function() {
		var post = [],
			elem,
			popped,
			prep,
			pre,
			ele;
		var stack = [ { value: '(', type: 4, pre: 0 } ];
		var arr = this.value;
		var i;
		var flag;
		for ( i = 1; i < arr.length; i++ ) {
			if ( arr[ i ].type === 1 || arr[ i ].type === 3 || arr[ i ].type === 13 ) {
				//if token is number,constant,or n(which is also a special constant in our case)
				if ( arr[ i ].type === 1 ) {
					arr[ i ].value = Number( arr[ i ].value );
				}
				post.push( arr[ i ] );
			} else if ( arr[ i ].type === 4 ) {
				stack.push( arr[ i ] );
			} else if ( arr[ i ].type === 5 ) {
				while ( ( popped = stack.pop() ).type !== 4 ) {
					post.push( popped );
				}
			} else if ( arr[ i ].type === 11 ) {
				while ( ( popped = stack.pop() ).type !== 4 ) {
					post.push( popped );
				}
				stack.push( popped );
			} else {
				elem = arr[ i ];
				pre = elem.pre;
				ele = stack[ stack.length - 1 ];
				prep = ele.pre;
				flag = ele.value === 'Math.pow' && elem.value === 'Math.pow';
				if ( pre > prep ) {
					stack.push( elem );
				} else {
					while ( ( prep >= pre && ! flag ) || ( flag && pre < prep ) ) {
						popped = stack.pop();
						ele = stack[ stack.length - 1 ];
						post.push( popped );
						prep = ele.pre;
						flag = elem.value === 'Math.pow' && ele.value === 'Math.pow';
					}
					stack.push( elem );
				}
			}
		}
		return new TCMexp( post );
	};

	TCMexp.prototype.postfixEval = function( UserDefined ) {
		var stack;
		var pop1;
		var pop2;
		var pop3;
		var arr;
		var bool;
		var i;
		UserDefined = UserDefined || {};
		UserDefined.PI = Math.PI;
		UserDefined.E = Math.E;
		stack = [];
		arr = this.value;
		bool = typeof UserDefined.n !== 'undefined';
		for ( i = 0; i < arr.length; i++ ) {
			if ( arr[ i ].type === 1 ) {
				stack.push( { value: arr[ i ].value, type: 1 } );
			} else if ( arr[ i ].type === 3 ) {
				stack.push( { value: UserDefined[ arr[ i ].value ], type: 1 } );
			} else if ( arr[ i ].type === 0 ) {
				if ( typeof stack[ stack.length - 1 ].type === 'undefined' ) {
					stack[ stack.length - 1 ].value.push( arr[ i ] );
				} else {
					stack[ stack.length - 1 ].value = arr[ i ].value( stack[ stack.length - 1 ].value );
				}
			} else if ( arr[ i ].type === 7 ) {
				if ( typeof stack[ stack.length - 1 ].type === 'undefined' ) {
					stack[ stack.length - 1 ].value.push( arr[ i ] );
				} else {
					stack[ stack.length - 1 ].value = arr[ i ].value( stack[ stack.length - 1 ].value );
				}
			} else if ( arr[ i ].type === 8 ) {
				pop1 = stack.pop();
				pop2 = stack.pop();
				stack.push( { type: 1, value: arr[ i ].value( pop2.value, pop1.value ) } );
			} else if ( arr[ i ].type === 10 ) {
				pop1 = stack.pop();
				pop2 = stack.pop();
				if ( typeof pop2.type === 'undefined' ) {
					pop2.value = pop2.concat( pop1 );
					pop2.value.push( arr[ i ] );
					stack.push( pop2 );
				} else if ( typeof pop1.type === 'undefined' ) {
					pop1.unshift( pop2 );
					pop1.push( arr[ i ] );
					stack.push( pop1 );
				} else {
					stack.push( { type: 1, value: arr[ i ].value( pop2.value, pop1.value ) } );
				}
			} else if ( arr[ i ].type === 2 || arr[ i ].type === 9 ) {
				pop1 = stack.pop();
				pop2 = stack.pop();
				if ( typeof pop2.type === 'undefined' ) {
					pop2 = pop2.concat( pop1 );
					pop2.push( arr[ i ] );
					stack.push( pop2 );
				} else if ( typeof pop1.type === 'undefined' ) {
					pop1.unshift( pop2 );
					pop1.push( arr[ i ] );
					stack.push( pop1 );
				} else {
					stack.push( { type: 1, value: arr[ i ].value( pop2.value, pop1.value ) } );
				}
			} else if ( arr[ i ].type === 12 ) {
				pop1 = stack.pop();
				if ( typeof pop1.type !== 'undefined' ) {
					pop1 = [ pop1 ];
				}
				pop2 = stack.pop();
				pop3 = stack.pop();
				stack.push( { type: 1, value: arr[ i ].value( pop3.value, pop2.value, new TCMexp( pop1 ) ) } );
			} else if ( arr[ i ].type === 13 ) {
				if ( bool ) {
					stack.push( { value: UserDefined[ arr[ i ].value ], type: 3 } );
				} else {
					stack.push( [ arr[ i ] ] );
				}
			}
		}
		if ( stack.length > 1 ) {
			throw new TCMexp.Exception( 'Uncaught Syntax error' );
		}
		return stack[ 0 ].value > 1000000000000000 ? 'Infinity' : parseFloat( stack[ 0 ].value.toFixed( 15 ) );
	};
	TCMexp.eval = function( str, tokens, obj ) {
		if ( typeof tokens === 'undefined' ) {
			return this.lex( str ).toPostfix().postfixEval();
		} else if ( typeof obj === 'undefined' ) {
			if ( typeof tokens.length !== 'undefined' ) {
				return this.lex( str, tokens ).toPostfix().postfixEval();
			}
			return this.lex( str ).toPostfix().postfixEval( tokens );
		}
		return this.lex( str, tokens ).toPostfix().postfixEval( obj );
	};

	window.tcmexp = TCMexp;
}( window ) );
